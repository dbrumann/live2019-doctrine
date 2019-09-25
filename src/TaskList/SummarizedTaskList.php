<?php

namespace App\TaskList;

use DateTimeImmutable;

class SummarizedTaskList
{
    private $id;
    private $name;
    private $archived;
    private $created;
    private $updated;
    private $taskCount;

    public function __construct(int $id, string $name, bool $archived, DateTimeImmutable $created, ?DateTimeImmutable $updated, int $taskCount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->archived = $archived;
        $this->created = $created;
        $this->updated = $updated;
        $this->taskCount = $taskCount;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    public function getTaskCount(): int
    {
        return $this->taskCount;
    }
}
