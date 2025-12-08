<?php
namespace App\EventSubscriber;

use App\Event\MatchCreatedEvent;
use App\Service\GamificationService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MatchCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GamificationService $gamificationService,
        private NotificationService $notificationService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            MatchCreatedEvent::NAME => 'onMatchCreated',
        ];
    }

    public function onMatchCreated(MatchCreatedEvent $event): void
    {
        $match = $event->getMatch();
        $userA = $match->getUserA();
        $userB = $match->getUserB();

        // Ajouter XP aux deux utilisateurs
        $this->gamificationService->addXp($userA, $this->gamificationService->getXpForMatch(), 'Match created');
        $this->gamificationService->addXp($userB, $this->gamificationService->getXpForMatch(), 'Match created');

        // Mettre à jour les scores du leaderboard
        $this->gamificationService->updateLeaderboardScore($userA);
        $this->gamificationService->updateLeaderboardScore($userB);

        // Envoyer des notifications aux deux utilisateurs
        $this->notificationService->createNotification(
            $userA,
            \App\Entity\Notification::TYPE_MATCH,
            [
                'matchId' => $match->getId(),
                'userId' => $userB->getId(),
                'username' => $userB->getUsername(),
                'avatarUrl' => $userB->getAvatarUrl()
            ]
        );

        $this->notificationService->createNotification(
            $userB,
            \App\Entity\Notification::TYPE_MATCH,
            [
                'matchId' => $match->getId(),
                'userId' => $userA->getId(),
                'username' => $userA->getUsername(),
                'avatarUrl' => $userA->getAvatarUrl()
            ]
        );

        // Une conversation est créée automatiquement avec la relation Message ↔ Match
        // Pas besoin de créer une entité Conversation séparée

        $this->em->flush();
    }
}

