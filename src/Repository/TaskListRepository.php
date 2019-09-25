<?php

namespace App\Repository;

use App\Entity\TaskList;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * @method TaskList[] findAll()
 */
class TaskListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskList::class);
    }

    public function findAllFor(User $user)
    {
        $sql = <<<SQL
SELECT list.*
FROM app_task_list AS list
LEFT JOIN task_list_user AS contributor ON list.id = contributor.task_list_id
WHERE list.owner_id = :user OR contributor.user_id = :user
SQL;

        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(TaskList::class, 'list');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);

        return $query
            ->setParameter('user', $user)
            ->getResult();
    }

    public function findListsOwnedBy(User $owner)
    {
        $dql = <<<DQL
SELECT task_list
FROM App\Entity\TaskList task_list
WHERE task_list.owner = :owner
DQL;

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('owner', $owner)
            ->getResult();
    }

    public function findListsContributedBy(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('task_list');

        return $queryBuilder
            ->join('task_list.contributors', 'contributors')
            ->where('contributors = :contributor')
            ->setParameter('contributor', $user)
            ->getQuery()
            ->getResult();
    }

    public function findActive(User $owner)
    {
        return $this->createQueryBuilder('task_list')
            ->where('task_list.archived = false')
            ->andWhere('task_list.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }

    public function findArchived(User $owner)
    {
        return $this->createQueryBuilder('task_list')
            ->where('task_list.archived = true AND task_list.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }
}
