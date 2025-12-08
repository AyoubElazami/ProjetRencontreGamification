<?php
namespace App\Controller\Matchmaking;

use App\Entity\UserMatch;
use App\Entity\Message;
use App\Service\GamificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MatchController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private GamificationService $gamificationService
    ) {}

    #[Route('/api/matches', name: 'api_matches_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function list(): JsonResponse
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $matches = $currentUser->getMatches();

        return $this->json([
            'matches' => array_map(function(UserMatch $m) use ($currentUser) {
                return $this->serializeMatch($m, $currentUser);
            }, $matches->toArray())
        ]);
    }

    #[Route('/api/matches/{id}', name: 'api_match_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(int $id): JsonResponse
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $match = $this->em->getRepository(UserMatch::class)->find($id);
        if (!$match) {
            throw new NotFoundHttpException('Match not found');
        }

        // Vérifier que l'utilisateur fait partie du match
        if ($match->getUserA()->getId() !== $currentUser->getId() 
            && $match->getUserB()->getId() !== $currentUser->getId()) {
            throw new NotFoundHttpException('Match not found');
        }

        return $this->json($this->serializeMatch($match, $currentUser));
    }

    #[Route('/api/matches/{id}/messages', name: 'api_match_messages', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getMessages(int $id, Request $request): JsonResponse
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $match = $this->em->getRepository(UserMatch::class)->find($id);
        if (!$match) {
            throw new NotFoundHttpException('Match not found');
        }

        if ($match->getUserA()->getId() !== $currentUser->getId() 
            && $match->getUserB()->getId() !== $currentUser->getId()) {
            throw new NotFoundHttpException('Match not found');
        }

        $limit = min(100, max(1, (int)$request->query->get('limit', 50)));
        $offset = max(0, (int)$request->query->get('offset', 0));

        $messages = $match->getMessages()->slice($offset, $limit);

        return $this->json([
            'messages' => array_map(fn(Message $message) => $this->serializeMessage($message), $messages)
        ]);
    }

    #[Route('/api/matches/{id}/message', name: 'api_match_send_message', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function sendMessage(int $id, Request $request): JsonResponse
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();

        $match = $this->em->getRepository(UserMatch::class)->find($id);
        if (!$match) {
            throw new NotFoundHttpException('Match not found');
        }

        if ($match->getUserA()->getId() !== $currentUser->getId() 
            && $match->getUserB()->getId() !== $currentUser->getId()) {
            throw new NotFoundHttpException('Match not found');
        }

        $data = json_decode($request->getContent(), true);
        $content = $data['content'] ?? null;

        if (!$content || empty(trim($content))) {
            return $this->json(['error' => 'Message content is required'], 400);
        }

        $message = new Message();
        $message->setUserMatch($match);
        $message->setSender($currentUser);
        $message->setContent(trim($content));

        // Mettre à jour la dernière interaction
        $match->setLastInteractionAt(new \DateTimeImmutable());

        $this->em->persist($message);
        $this->em->flush();

        // Ajouter XP pour message
        $this->gamificationService->addXp($currentUser, $this->gamificationService->getXpForMessage(), 'Message sent');

        return $this->json($this->serializeMessage($message), 201);
    }

    private function serializeMatch(UserMatch $match, \App\Entity\User $currentUser): array
    {
        $otherUser = $match->getUserA()->getId() === $currentUser->getId() 
            ? $match->getUserB() 
            : $match->getUserA();

        return [
            'id' => $match->getId(),
            'user' => [
                'id' => $otherUser->getId(),
                'username' => $otherUser->getUsername(),
                'avatarUrl' => $otherUser->getAvatarUrl(),
                'bio' => $otherUser->getBio()
            ],
            'createdAt' => $match->getCreatedAt()?->format('c'),
            'lastInteractionAt' => $match->getLastInteractionAt()?->format('c')
        ];
    }

    private function serializeMessage(Message $message): array
    {
        return [
            'id' => $message->getId(),
            'senderId' => $message->getSender()->getId(),
            'content' => $message->getContent(),
            'readAt' => $message->getReadAt()?->format('c'),
            'createdAt' => $message->getCreatedAt()?->format('c')
        ];
    }
}

