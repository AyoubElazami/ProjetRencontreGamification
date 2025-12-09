<?php
namespace App\Service;

use App\Entity\UserMatch;
use App\Entity\MatchRequest;
use App\Entity\User;
use App\Entity\Notification;
use App\Event\MatchCreatedEvent;
use App\Repository\UserMatchRepository;
use App\Repository\MatchRequestRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MatchService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MatchRequestRepository $matchRequestRepo,
        private UserMatchRepository $matchRepo,
        private EventDispatcherInterface $eventDispatcher,
        private NotificationService $notificationService
    ) {}

    public function createMatchRequest(User $fromUser, User $toUser, string $type = MatchRequest::TYPE_LIKE): MatchRequest
    {
        // Vérifier si un match existe déjà
        $existingMatch = $this->matchRepo->findMatchBetweenUsers($fromUser, $toUser);
        if ($existingMatch) {
            throw new \Exception('A match already exists between these users.');
        }

        // Vérifier s'il existe déjà une requête dans le même sens (fromUser→toUser)
        $existingRequestSameDirection = $this->matchRequestRepo->createQueryBuilder('mr')
            ->where('mr.fromUser = :fromUser')
            ->andWhere('mr.toUser = :toUser')
            ->andWhere('mr.status = :status')
            ->setParameter('fromUser', $fromUser)
            ->setParameter('toUser', $toUser)
            ->setParameter('status', MatchRequest::STATUS_PENDING)
            ->getQuery()
            ->getOneOrNullResult();

        if ($existingRequestSameDirection) {
            throw new \Exception('A match request already exists between these users.');
        }

        // Vérifier s'il existe une requête dans l'autre sens (toUser→fromUser) - c'est OK, on peut liker en retour
        $reciprocalRequest = $this->matchRequestRepo->findPendingLike($toUser, $fromUser);

        $matchRequest = new MatchRequest();
        $matchRequest->setFromUser($fromUser);
        $matchRequest->setToUser($toUser);
        $matchRequest->setType($type);
        $matchRequest->setStatus(MatchRequest::STATUS_PENDING);

        $this->em->persist($matchRequest);
        $this->em->flush();

        // Si l'autre utilisateur a déjà liké et que c'est un like/superlike, créer le match
        if ($reciprocalRequest && in_array($matchRequest->getType(), [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE])) {
            $this->createMatch($fromUser, $toUser);
            
            // Marquer les requêtes comme matchées
            $matchRequest->setStatus(MatchRequest::STATUS_MATCHED);
            $reciprocalRequest->setStatus(MatchRequest::STATUS_MATCHED);
            $this->em->flush();
        } else {
            // Si pas de match immédiat, créer une notification pour l'utilisateur qui a reçu le like/superlike
            // (pas de notification pour dislike ou wink)
            if (in_array($matchRequest->getType(), [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE])) {
                $this->notificationService->createNotification(
                    $toUser,
                    $matchRequest->getType() === MatchRequest::TYPE_SUPERLIKE ? Notification::TYPE_SUPERLIKE : Notification::TYPE_LIKE,
                    [
                        'userId' => $fromUser->getId(),
                        'username' => $fromUser->getUsername(),
                        'avatarUrl' => $fromUser->getAvatarUrl(),
                        'matchRequestId' => $matchRequest->getId()
                    ]
                );
            }
        }

        return $matchRequest;
    }

    public function createMatch(User $userA, User $userB): UserMatch
    {
        $match = new UserMatch();
        $match->setUserA($userA);
        $match->setUserB($userB);

        $this->em->persist($match);
        $this->em->flush();

        // Publier l'événement de match créé
        $event = new MatchCreatedEvent($match);
        $this->eventDispatcher->dispatch($event, MatchCreatedEvent::NAME);

        return $match;
    }

    public function getUserMatches(User $user): array
    {
        return $this->matchRepo->findUserMatches($user);
    }
}

