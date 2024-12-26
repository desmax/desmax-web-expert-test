<?php

declare(strict_types=1);

namespace App\App\Scheduler\WeeklyStats;

use DateTimeImmutable;

final readonly class SendWeeklyStatsMessage
{
    public function __construct(
        public DateTimeImmutable $dateFrom,
        public DateTimeImmutable $dateTo,
    ) {
    }
}
