<?php
namespace App\Service;

use App\Entity\UserMatch;
use App\Entity\MatchRequest;
use App\Entity\User;
use App\Event\MatchCreatedEvent;
use App\Repository\UserMatchRepository;
use App\Repository\MatchRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MatchService
{
    public function __construct(
        private EntityManagerInterface $em,
        private MatchRequestRepository $matchRequestRepo,
        private UserMatchRepository $matchRepo,
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function createMatchRequest(User $fromUser, User $toUser, string $type = MatchRequest::TYPE_LIKE): MatchRequest
    {
        // Vérifier si une requête existe déjà
        $existingRequest = $this->matchRequestRepo->findPendingBetweenUsers($fromUser, $toUser);
        if ($existingRequest) {
            throw new \Exception('A match request already exists between these users.');
        }

        // Vérifier si un match existe déjà
        $existingMatch = $this->matchRepo->findMatchBetweenUsers($fromUser, $toUser);
        if ($existingMatch) {
            throw new \Exception('A match already exists between these users.');
        }

        $matchRequest = new MatchRequest();
        $matchRequest->setFromUser($fromUser);
        $matchRequest->setToUser($toUser);
        $matchRequest->setType($type);
        $matchRequest->setStatus(MatchRequest::STATUS_PENDING);

        $this->em->persist($matchRequest);
        $this->em->flush();

        // Vérifier si l'autre utilisateur a déjà liké (match réciproque)
        $reciprocalRequest = $this->matchRequestRepo->findPendingLike($toUser, $fromUser);
        if ($reciprocalRequest && in_array($matchRequest->getType(), [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE])) {
            $this->createMatch($fromUser, $toUser);
            
            // Marquer les requêtes comme matchées
            $matchRequest->setStatus(MatchRequest::STATUS_MATCHED);
            $reciprocalRequest->setStatus(MatchRequest::STATUS_MATCHED);
            $this->em->flush();
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

