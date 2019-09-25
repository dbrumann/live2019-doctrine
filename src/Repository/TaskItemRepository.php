<?php

namespace App\Repository;

use App\Entity\TaskItem;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TaskItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskItem::class);
    }

    public function findTasksCreatedToday()
    {
        $dql = <<<DQL
SELECT task
FROM App\Entity\TaskItem task
WHERE task.created BETWEEN :startdate AND :enddate
DQL;

        return $this->getEntityManager()->createQuery($dql)
            ->setParameter('startdate', new DateTimeImmutable('today'))
            ->setParameter('enddate', new DateTimeImmutable())
            ->getResult();
    }
}
