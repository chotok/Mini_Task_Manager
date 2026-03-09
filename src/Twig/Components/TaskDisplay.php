<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Repository\TaskRepository;

#[AsLiveComponent]
final class TaskDisplay
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public ?string $queryTitle = null;

    public function __construct(private TaskRepository $tasksRepo)
    {
    }

    public function getTasks(): array
    {
        return $this->tasksRepo->findAll($this->queryTitle);
    }
}
