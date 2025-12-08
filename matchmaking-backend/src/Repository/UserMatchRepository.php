<?php
namespace App\Repository;

use App\Entity\UserMatch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMatch::class);
    }

    public function findMatchBetweenUsers(User $userA, User $userB): ?UserMatch
    {
        return $this->createQueryBuilder('m')
            ->where('(m.userA = :userA AND m.userB = :userB)')
            ->orWhere('(m.userA = :userB AND m.userB = :userA)')
            ->setParameter('userA', $userA)
            ->setParameter('userB', $userB)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserMatches(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.userA = :user OR m.userB = :user')
            ->setParameter('user', $user)
            ->orderBy('m.lastInteractionAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

