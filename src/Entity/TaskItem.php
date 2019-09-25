<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("NOTIFY")
 * @ORM\Table(name="app_task_item")
 */
class TaskItem implements NotifyPropertyChanged
{
    private $listeners = [];

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TaskList", inversedBy="items")
     */
    private $list;

    /**
     * @ORM\Column
     */
    private $summary;

    /**
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created;

    /**
     * @internal New TaskItems should only be generated through TaskList::addItem()
     */
    public function __construct(TaskList $taskList, string $summary)
    {
        $this->list = $taskList;
        $this->summary = $summary;

        $this->done = false;
        $this->created = new DateTimeImmutable();
    }

    public function reopen(): void
    {
        if ($this->done === false) {
            throw new \RuntimeException('Task is already open.');
        }

        //$this->notifyListeners('done', true, false);
        $this->done = false;
    }

    public function close(): void
    {
        if ($this->done === true) {
            throw new \RuntimeException('Task is already done.');
        }

        $this->notifyListeners('done', false, true);
        $this->done = true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getList(): TaskList
    {
        return $this->list;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function getCreatedOn(): DateTimeImmutable
    {
        return $this->created;
    }

    private function notifyListeners(string $propertyName, $oldValue, $newValue): void
    {
        foreach ($this->listeners as $listener) {
            $listener->propertyChanged($this, $propertyName, $oldValue, $newValue);
        }
    }

    public function addPropertyChangedListener(PropertyChangedListener $listener): void
    {
        $this->listeners[] = $listener;
    }
}
