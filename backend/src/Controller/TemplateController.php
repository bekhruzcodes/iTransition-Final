<?php

namespace App\Controller;

use App\Entity\QuestionOption;
use App\Entity\Template;
use App\Entity\Question;
use App\Entity\Topic;
use App\Repository\TemplateRepository;
use App\Repository\QuestionRepository;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/templates')]
class TemplateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TemplateRepository $templateRepository,
        private QuestionRepository $questionRepository,
        private ValidatorInterface $validator
    ) {}

    private function validateEntity($entity): ?JsonResponse
    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }
        return null;
    }

    private function findTemplateOr404(int $id): ?Template
    {
        $template = $this->templateRepository->find($id);
        if (!$template) {
            throw new \Exception('Template not found', 404);
        }
        return $template;
    }

    private function findQuestionOr404(int $templateId, int $questionId): ?Question
    {
        $question = $this->questionRepository->find($questionId);
        if (!$question || $question->getTemplate()->getId() !== $templateId) {
            throw new \Exception('Question not found', 404);
        }
        return $question;
    }

    private function findOptionOr404(Question $question, int $optionId): ?QuestionOption
    {
        $option = $this->entityManager->getRepository(QuestionOption::class)->find($optionId);
        if (!$option || $option->getQuestion() !== $question) {
            throw new \Exception('Option not found', 404);
        }
        return $option;
    }

    private function handleException(\Exception $e): JsonResponse
    {
        $statusCode = $e->getCode() ?: 400;
        return $this->json([
            'error' => $statusCode === 404 ? $e->getMessage() : 'Operation failed',
            'message' => $statusCode === 404 ? null : $e->getMessage()
        ], $statusCode);
    }

    #[Route('/list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $templates = $this->templateRepository->findAll();
            return $this->json(['templates' => $templates]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Check if JSON was parsed successfully
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return $this->json([
                    'error' => 'Invalid JSON',
                    'message' => json_last_error_msg()
                ], 400);
            }

            // Handle nested template structure
            $templateData = $data['template'] ?? $data;

            // Validate required fields
            if (!isset($templateData['title'])) {
                return $this->json([
                    'error' => 'Missing required field',
                    'message' => 'Template title is required'
                ], 400);
            }

            // Begin transaction
            $this->entityManager->beginTransaction();

            try {
                $template = new Template();
                $topicRepository = $this->entityManager->getRepository(Topic::class);
                $topic = $topicRepository->find($templateData['topic_id'] ?? 1); // TODO: Need to handle topic better

                if (!$topic) {
                    throw new \Exception("Topic not found!");
                }

                $template->setTitle($templateData['title'])
                    ->setDescription($templateData['description'] ?? '')
                    ->setUser($this->getUser())
                    ->setTopic($topic);

                if ($error = $this->validateEntity($template)) {
                    return $error;
                }


                $this->entityManager->persist($template);
                $this->entityManager->flush();

                $questionsData = [];

                // Process questions if provided
                if (isset($data['questions']) && is_array($data['questions'])) {
                    foreach ($data['questions'] as $idx => $questionData) {
                        // Validate required question fields
                        if (!isset($questionData['text']) || !isset($questionData['type'])) {
                            throw new \InvalidArgumentException("Question #{$idx}: missing required fields (text or type)");
                        }

                        $question = new Question();
                        $question->setTemplate($template)
                            ->setText($questionData['text'])
                            ->setType($questionData['type'])
                            ->setRequired($questionData['required'] ?? false)
                            ->setOrderNum($questionData['orderNum'] ?? ($idx + 1));

                        if ($error = $this->validateEntity($question)) {
                            throw new \Exception("Question validation failed: " . json_encode($error->getData()['errors']), 400);
                        }

                        $this->entityManager->persist($question);

                        $optionsData = [];

                        // Process options if provided
                        if (isset($questionData['options']) && is_array($questionData['options'])) {
                            foreach ($questionData['options'] as $optIdx => $optionData) {
                                // Validate required option fields
                                if (!isset($optionData['text'])) {
                                    throw new \InvalidArgumentException("Question #{$idx}, Option #{$optIdx}: missing text");
                                }

                                $option = new QuestionOption();
                                $option->setText($optionData['text'])
                                    ->setValue($optionData['value'] ?? strtolower(str_replace(' ', '_', $optionData['text'])))
                                    ->setOrderNum($optionData['orderNum'] ?? ($optIdx + 1))
                                    ->setQuestion($question);

                                if ($error = $this->validateEntity($option)) {
                                    throw new \Exception("Option validation failed: " . json_encode($error->getData()['errors']), 400);
                                }

                                $this->entityManager->persist($option);

                                $optionsData[] = [
                                    'text' => $option->getText(),
                                    'value' => $option->getValue(),
                                    'orderNum' => $option->getOrderNum()
                                ];
                            }
                        }

                        $questionsData[] = [
                            'text' => $question->getText(),
                            'type' => $question->getType(),
                            'required' => $question->isRequired(),
                            'orderNum' => $question->getOrderNum(),
                            'options' => $optionsData
                        ];
                    }
                }

                $this->entityManager->flush();
                $this->entityManager->commit();

                return $this->json([
                    'message' => 'Template created successfully',
                    'template' => [
                        'id' => $template->getId(),
                        'title' => $template->getTitle(),
                        'description' => $template->getDescription(),
                        'questions' => $questionsData
                    ]
                ], 201);
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                throw $e; // Re-throw to be caught by outer try/catch
            }
        } catch (\Exception $e) {
            // More detailed error handling
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

            // Log the error for debugging
            error_log('Template creation error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());

            return $this->json([
                'error' => 'Operation failed',
                'message' => $e->getMessage(),
                'code' => $statusCode
            ], $statusCode);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $template = $this->findTemplateOr404($id);

            $questionsData = [];
            foreach ($template->getQuestions() as $question) {
                $options = [];
                foreach ($question->getOptions() as $option) {
                    $options[] = [
                        'id' => $option->getId(),
                        'text' => $option->getText(),
                        'value' => $option->getValue(),
                        'orderNum' => $option->getOrderNum()
                    ];
                }

                $questionsData[] = [
                    'id' => $question->getId(),
                    'text' => $question->getText(),
                    'type' => $question->getType(),
                    'required' => $question->isRequired(),
                    'orderNum' => $question->getOrderNum(),
                    'options' => $options
                ];
            }

            return $this->json([
                'template' => [
                    'id' => $template->getId(),
                    'title' => $template->getTitle(),
                    'description' => $template->getDescription(),
                    'questions' => $questionsData
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions', methods: ['POST'])]
    public function addQuestion(int $id, Request $request): JsonResponse
    {
        try {
            $template = $this->findTemplateOr404($id);
            $data = json_decode($request->getContent(), true);

            $question = new Question();
            $question->setTemplate($template)
                ->setText($data['text'])
                ->setType($data['type'])
                ->setRequired($data['required'] ?? false)
                ->setOrderNum(count($template->getQuestions()) + 1);

            if ($error = $this->validateEntity($question)) {
                return $error;
            }

            // Add default option for choice-type questions
            if (in_array($data['type'], ['radio', 'checkbox', 'dropdown'])) {
                $option = new QuestionOption();
                $option->setText('Option 1')
                    ->setValue('option_1')
                    ->setOrderNum(1)
                    ->setQuestion($question);
                $this->entityManager->persist($option);
            }

            $this->entityManager->persist($question);
            $this->entityManager->flush();

            $options = $question->getOptions()->map(fn($opt) => [
                'id' => $opt->getId(),
                'text' => $opt->getText(),
                'value' => $opt->getValue(),
                'orderNum' => $opt->getOrderNum()
            ])->toArray();

            return $this->json([
                'question' => [
                    'id' => $question->getId(),
                    'text' => $question->getText(),
                    'type' => $question->getType(),
                    'required' => $question->isRequired(),
                    'orderNum' => $question->getOrderNum(),
                    'options' => $options
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}/text', methods: ['PATCH'])]
    public function updateQuestionText(int $id, int $questionId, Request $request): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $data = json_decode($request->getContent(), true);

            $question->setText($data['text']);

            if ($error = $this->validateEntity($question)) {
                return $error;
            }

            $this->entityManager->flush();
            return $this->json([
                'question' => [
                    'id' => $question->getId(),
                    'text' => $question->getText()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}/required', methods: ['PATCH'])]
    public function updateQuestionRequired(int $id, int $questionId, Request $request): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $data = json_decode($request->getContent(), true);

            $question->setRequired($data['required']);
            $this->entityManager->flush();

            return $this->json([
                'question' => [
                    'id' => $question->getId(),
                    'required' => $question->isRequired()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}/options', methods: ['POST'])]
    public function addOption(int $id, int $questionId, Request $request): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $data = json_decode($request->getContent(), true);

            $option = new QuestionOption();
            $option->setText($data['text'])
                ->setValue($data['value'])
                ->setOrderNum(count($question->getOptions()) + 1)
                ->setQuestion($question);

            if ($error = $this->validateEntity($option)) {
                return $error;
            }

            $this->entityManager->persist($option);
            $this->entityManager->flush();

            return $this->json([
                'option' => [
                    'id' => $option->getId(),
                    'text' => $option->getText(),
                    'value' => $option->getValue(),
                    'orderNum' => $option->getOrderNum()
                ]
            ], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}/options/{optionId}', methods: ['PATCH'])]
    public function updateOption(int $id, int $questionId, int $optionId, Request $request): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $option = $this->findOptionOr404($question, $optionId);
            $data = json_decode($request->getContent(), true);

            $option->setText($data['text']);
            $option->setValue(strtolower(str_replace(' ', '_', $data['text'])));

            if ($error = $this->validateEntity($option)) {
                return $error;
            }

            $this->entityManager->flush();
            return $this->json([
                'option' => [
                    'id' => $option->getId(),
                    'text' => $option->getText(),
                    'value' => $option->getValue()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}', methods: ['DELETE'])]
    public function deleteQuestion(int $id, int $questionId): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $this->entityManager->remove($question);
            $this->entityManager->flush();

            return $this->json(['message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    #[Route('/{id}/questions/{questionId}/options/{optionId}', methods: ['DELETE'])]
    public function deleteOption(int $id, int $questionId, int $optionId): JsonResponse
    {
        try {
            $question = $this->findQuestionOr404($id, $questionId);
            $option = $this->findOptionOr404($question, $optionId);

            $this->entityManager->remove($option);
            $this->entityManager->flush();

            return $this->json(['message' => 'Option deleted successfully']);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}