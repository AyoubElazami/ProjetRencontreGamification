<?php
namespace App\Repository;

use App\Entity\MatchRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MatchRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchRequest::class);
    }

    public function findPendingBetweenUsers(User $userA, User $userB): ?MatchRequest
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.fromUser = :userA AND mr.toUser = :userB')
            ->orWhere('mr.fromUser = :userB AND mr.toUser = :userA')
            ->andWhere('mr.status = :status')
            ->setParameter('userA', $userA)
            ->setParameter('userB', $userB)
            ->setParameter('status', MatchRequest::STATUS_PENDING)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPendingLike(User $fromUser, User $toUser): ?MatchRequest
    {
        return $this->createQueryBuilder('mr')
            ->where('mr.fromUser = :fromUser')
            ->andWhere('mr.toUser = :toUser')
            ->andWhere('mr.status = :status')
            ->andWhere('mr.type IN (:types)')
            ->setParameter('fromUser', $fromUser)
            ->setParameter('toUser', $toUser)
            ->setParameter('status', MatchRequest::STATUS_PENDING)
            ->setParameter('types', [MatchRequest::TYPE_LIKE, MatchRequest::TYPE_SUPERLIKE])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

