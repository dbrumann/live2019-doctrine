<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="app_task_list")
 */
class TaskList
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\Column
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TaskItem", mappedBy="list", cascade={"persist"})
     */
    private $items;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $contributors;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $lastUpdated;

    public function __construct(User $owner, string $name)
    {
        $this->owner = $owner;
        $this->name = $name;

        $this->archived = false;
        $this->created = new DateTimeImmutable();
        $this->items = new ArrayCollection();
        $this->contributors = new ArrayCollection();
    }

    public function archive(): void
    {
        $this->archived = true;
    }

    public function addItem(string $summary): void
    {
        $this->items->add(new TaskItem($this, $summary));
        $this->lastUpdated = new DateTimeImmutable();
    }

    public function addContributor(User $user): void
    {
        $this->contributors->add($user);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isArchived(): bool
    {
        return $this->archived === true;
    }

    /**
     * @return TaskItem[]
     */
    public function getItems(): array
    {
        return $this->items->toArray();
    }

    /**
     * @return User[]
     */
    public function getContributors(): array
    {
        return $this->contributors->toArray();
    }

    public function getCreatedOn(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getLastUpdatedOn(): ?DateTimeImmutable
    {
        return $this->lastUpdated;
    }
}
