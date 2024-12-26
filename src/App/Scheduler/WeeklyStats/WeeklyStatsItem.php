<?php

declare(strict_types=1);

namespace App\App\Scheduler\WeeklyStats;

final readonly class WeeklyStatsItem
{
    public function __construct(
        public string $id,
        public string $title,
        public string $author,
        /** @var string[] */
        public array $categories,
        public int $commentCount,
    ) {
    }
}
