<?php
namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findByTeam($teamId)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.team = :teamId')
            ->setParameter('teamId', $teamId)
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUpcomingEvents($teamId)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.team = :teamId')
            ->andWhere('e.date >= :today')
            ->setParameter('teamId', $teamId)
            ->setParameter('today', new \DateTime())
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
