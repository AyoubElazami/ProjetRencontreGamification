<?php
namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
	private EntityManagerInterface $em;
	private UserPasswordHasherInterface $passwordHasher;
	private JWTTokenManagerInterface $jwtManager;

	public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager)
	{
		$this->em = $em;
		$this->passwordHasher = $passwordHasher;
		$this->jwtManager = $jwtManager;
	}

	#[Route('/api/login', name: 'api_login', methods: ['POST'])]
	public function login(Request $request): Response
	{
		$data = json_decode($request->getContent(), true);

		$email = $data['email'] ?? null;
		$password = $data['password'] ?? null;

		if (!$email || !$password) {
			return new JsonResponse(['error' => 'Email and password are required.'], 400);
		}

		$user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
		if (!$user) {
			return new JsonResponse(['error' => 'Invalid credentials.'], 401);
		}

		if (!$this->passwordHasher->isPasswordValid($user, $password)) {
			return new JsonResponse(['error' => 'Invalid credentials.'], 401);
		}

		// create JWT token
		$token = $this->jwtManager->create($user);

		// If the client expects HTML, redirect to index page (front-end)
		$accept = $request->headers->get('accept', '');
		if (str_contains(strtolower($accept), 'text/html')) {
			// Redirect to root (index). Adjust route if you have a named route for index.
			return $this->redirect('/');
		}

		return new JsonResponse([
			'token' => $token,
			'user' => [
				'id' => $user->getId(),
				'email' => $user->getEmail(),
				'username' => $user->getUsername(),
			]
		]);
	}
}
