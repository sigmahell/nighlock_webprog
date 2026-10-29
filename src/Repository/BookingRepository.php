<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Coach;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Fetch all bookings for a student / client
     *
     * @return Booking[]
     */
    public function findByClient(User $client): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.coach', 'c')
            ->leftJoin('b.session', 's')
            ->addSelect('c', 's')
            ->andWhere('b.client = :client')
            ->setParameter('client', $client)
            ->orderBy('b.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch all bookings for a coach
     *
     * @return Booking[]
     */
    public function findByCoach(Coach $coach): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'u')
            ->leftJoin('b.session', 's')
            ->addSelect('u', 's')
            ->andWhere('b.coach = :coach')
            ->setParameter('coach', $coach)
            ->orderBy('b.scheduledAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch pending bookings for a coach (awaiting accept / deny)
     *
     * @return Booking[]
     */
    public function findPendingForCoach(Coach $coach): array
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.client', 'u')
            ->leftJoin('b.session', 's')
            ->addSelect('u', 's')
            ->andWhere('b.coach = :coach')
            ->andWhere('b.status = :status')
            ->setParameter('coach', $coach)
            ->setParameter('status', 'pending')
            ->orderBy('b.scheduledAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get booking statistics for admin dashboard
     *
     * @return array{total: int, pending: int, accepted: int}
     */
    public function getBookingStatistics(): array
    {
        $qb = $this->createQueryBuilder('b');

        $total = (int) (clone $qb)
            ->select('COUNT(b.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $pending = (int) (clone $qb)
            ->select('COUNT(b.id)')
            ->andWhere('LOWER(b.status) = :status')
            ->setParameter('status', 'pending')
            ->getQuery()
            ->getSingleScalarResult();

        $accepted = (int) (clone $qb)
            ->select('COUNT(b.id)')
            ->andWhere('LOWER(b.status) = :status')
            ->setParameter('status', 'accepted')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => $total,
            'pending' => $pending,
            'accepted' => $accepted,
        ];
    }
}
