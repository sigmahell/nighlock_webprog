<?php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return Notification[]
     */
    public function findForRecipient(User $recipient): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.booking', 'b')
            ->addSelect('b')
            ->andWhere('n.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadForRecipient(User $recipient): int
    {
        return (int) $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->andWhere('n.recipient = :recipient')
            ->andWhere('n.isRead = :isRead')
            ->setParameter('recipient', $recipient)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
