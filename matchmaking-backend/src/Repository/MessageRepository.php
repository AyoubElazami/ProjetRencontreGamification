<?php
namespace App\Repository;

use App\Entity\Message;
use App\Entity\UserMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessagesByMatch(UserMatch $match, int $limit = 50, int $offset = 0): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.userMatch = :match')
            ->setParameter('match', $match)
            ->orderBy('m.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}

