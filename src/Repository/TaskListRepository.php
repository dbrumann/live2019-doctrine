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

    public function findSummarizedTaskListFor(User $user)
    {
        $dql = <<<DQL
SELECT NEW App\TaskList\SummarizedTaskList(
    list.id,
    list.name,
    list.archived,
    list.created,
    list.lastUpdated,
    SIZE(list.items)
)
FROM App\Entity\TaskList list
WHERE list.owner = :owner
DQL;

        return $this->getEntityManager()
            ->createQuery($dql)
            ->useResultCache(true, 3600, 'frontpage_summarized')
            ->setParameter('owner', $user)
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
            ->useResultCache(true, 3600, 'frontpage_owned')
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
            ->useResultCache(true, 3600, 'frontpage_contributed')
            ->getResult();
    }

    public function findActive(User $owner)
    {
        return $this->createQueryBuilder('task_list')
            ->where('task_list.archived = false')
            ->andWhere('task_list.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->useResultCache(true, 3600, 'frontpage_active')
            ->getResult();
    }

    public function findArchived(User $owner)
    {
        return $this->createQueryBuilder('task_list')
            ->where('task_list.archived = true AND task_list.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->useResultCache(true, 3600, 'frontpage_archived')
            ->getResult();
    }
}
