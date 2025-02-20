<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\String\UnicodeString;

#[Route('/api')]
class ApiAuthController
{
    private Security $security;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        Security $security,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->security = $security;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->getJsonData($request);
            $errors = [];

            // Validate required fields
            $requiredFields = ['name', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $errors[$field] = "This field is required";
                }
            }

            // Validate email format
            if (!empty($data['email'])) {
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Invalid email format';
                } else {
                    // Check if user already exists
                    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy([
                        'email' => strtolower(trim($data['email']))
                    ]);
                    if ($existingUser) {
                        $errors['email'] = 'User with this email already exists';
                    }
                }
            }

            // Validate password strength
            if (!empty($data['password']) && !$this->isPasswordStrong($data['password'])) {
                $errors['password'] = 'Yov weak mindset, weak password huh ?!';
            }

            // Return all validation errors if any exist
            if (!empty($errors)) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Create and configure new user
            $user = new User();
            $user->setName(trim($data['name']));
            $user->setEmail(strtolower(trim($data['email'])));
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

            // Validate the entity
            $entityErrors = $this->validator->validate($user);
            if (count($entityErrors) > 0) {
                $errorMessages = [];
                foreach ($entityErrors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                return new JsonResponse([
                    'success' => false,
                    'errors' => $errorMessages
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Persist and save the user
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->success('User registered successfully', Response::HTTP_CREATED, [
                'user' => [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
                ]
            ]);

        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => ['json' => 'Invalid JSON data']
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Log the error here
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred during registration',
                'errors' => ['server' => 'Internal server error']
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            $data = $this->getJsonData($request);
            $errors = [];

            // Validate required fields
            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            }
            if (empty($data['password'])) {
                $errors['password'] = 'Password is required';
            }

            if (!empty($errors)) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => $errors
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Find user
            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'email' => strtolower(trim($data['email']))
            ]);

            if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => ['credentials' => 'Invalid credentials']
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Generate JWT token
            $token = $this->jwtManager->create($user);

            return new JsonResponse([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'email' => $user->getEmail()
                    ]
                ]
            ]);

        } catch (\JsonException $e) {
            return new JsonResponse([
                'success' => false,
                'errors' => ['json' => 'Invalid JSON data']
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Log the error here
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred during login',
                'errors' => ['server' => 'Internal server error']
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getJsonData(Request $request): array
    {
        $content = $request->getContent();
        if (empty($content)) {
            throw new \JsonException('Empty request body');
        }
        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    private function isPasswordStrong(string $password): bool
    {
        $password = new UnicodeString($password);

        return $password->length() >= 6 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password);
    }

    private function success(string $message, int $status = Response::HTTP_OK, array $data = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $status);
    }
}