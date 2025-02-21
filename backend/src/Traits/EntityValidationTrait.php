<?php

namespace App\Traits;

use App\Entity\Question;
use App\Entity\QuestionOption;
use App\Entity\Template;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait EntityValidationTrait
{
    private function validateEntity(object $entity): ?JsonResponse
    {
        $errors = $this->getValidator()->validate($entity);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], 400);
        }
        return null;
    }

    private function findTemplateOr404(int $id): Template
    {
        $template = $this->getEntityManager()->getRepository(Template::class)->find($id);
        if (!$template) {
            throw new NotFoundHttpException('Template not found');
        }
        return $template;
    }

    private function findQuestionOr404(int $templateId, int $questionId): Question
    {
        $question = $this->getEntityManager()->getRepository(Question::class)->find($questionId);
        if (!$question || $question->getTemplate()->getId() !== $templateId) {
            throw new NotFoundHttpException('Question not found');
        }
        return $question;
    }

    private function findOptionOr404(Question $question, int $optionId): QuestionOption
    {
        $option = $this->getEntityManager()->getRepository(QuestionOption::class)->find($optionId);
        if (!$option || $option->getQuestion() !== $question) {
            throw new NotFoundHttpException('Option not found');
        }
        return $option;
    }

// Define abstract methods to enforce dependency contracts
    abstract protected function getValidator(): ValidatorInterface;

    abstract protected function getEntityManager(): EntityManagerInterface;
}
