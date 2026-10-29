<?php

namespace App\Repository;

use App\Entity\Coach;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coach>
 */
class CoachRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coach::class);
    }

    /**
     * Fetch approved coaches for a specific tier (featured, pro, community)
     *
     * @return Coach[]
     */
    public function findApprovedByTier(string $tier): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.sessions', 's')
            ->addSelect('s')
            ->andWhere('c.tier = :tier')
            ->andWhere('c.status = :status')
            ->setParameter('tier', $tier)
            ->setParameter('status', 'approved')
            ->orderBy('c.rating', 'DESC')
            ->addOrderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch all approved coaches grouped by tier
     *
     * @return Coach[]
     */
    public function findAllApproved(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.sessions', 's')
            ->addSelect('s')
            ->andWhere('c.status = :status')
            ->setParameter('status', 'approved')
            ->orderBy('c.rating', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Fetch coach applications pending review
     *
     * @return Coach[]
     */
    public function findPendingApplications(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
