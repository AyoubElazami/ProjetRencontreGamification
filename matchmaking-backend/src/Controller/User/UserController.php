<?php
namespace App\Controller\User;

use App\Entity\User;
use App\Service\RecommendationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private RecommendationService $recommendationService
    ) {}

    #[Route('/api/users', name: 'api_users_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(50, max(1, (int)$request->query->get('limit', 20)));
        $offset = ($page - 1) * $limit;

        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
            ->from(User::class, 'u')
            ->where('u.id != :currentUser')
            ->setParameter('currentUser', $this->getUser()->getId())
            ->orderBy('u.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $users = $qb->getQuery()->getResult();

        return $this->json([
            'users' => array_map(fn(User $user) => $this->serializeUser($user), $users),
            'page' => $page,
            'limit' => $limit
        ]);
    }

    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->json($this->serializeUser($user));
    }

    #[Route('/api/recommendations', name: 'api_recommendations', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function recommendations(Request $request): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $limit = min(50, max(1, (int)$request->query->get('limit', 20)));

        $recommendations = $this->recommendationService->getRecommendations($currentUser, $limit);

        return $this->json([
            'users' => array_map(fn(User $user) => $this->serializeUser($user), $recommendations)
        ]);
    }

    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'bio' => $user->getBio(),
            'gender' => $user->getGender(),
            'age' => $user->getAge(),
            'location' => $user->getLocation(),
            'avatarUrl' => $user->getAvatarUrl(),
            'level' => $user->getLevel(),
            'xp' => $user->getXp(),
            'score' => $user->getScore(),
            'createdAt' => $user->getCreatedAt()?->format('c')
        ];
    }
}

