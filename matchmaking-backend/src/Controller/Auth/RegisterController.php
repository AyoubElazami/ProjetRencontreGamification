<?php
namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->validator = $validator;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $username = $data['username'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password are required.'], 400);
        }

        // check existing
        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return $this->json(['error' => 'Email already used.'], 409);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        // hash password
        $hashed = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashed);

        // validation
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $err) {
                $messages[] = $err->getPropertyPath() . ': ' . $err->getMessage();
            }
            return $this->json(['errors' => $messages], 400);
        }

        $this->em->persist($user);
        $this->em->flush();

        // create JWT token to return immediately
        $token = $this->jwtManager->create($user);

        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername()
            ]
        ], 201);
    }
}
