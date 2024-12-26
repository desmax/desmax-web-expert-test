<?php

declare(strict_types=1);

namespace App\App\Scheduler\WeeklyStats;

use App\App\News\NewsStatisticsService;
use App\App\Scheduler\MessageHandlerInterface;

final readonly class SendWeeklyStatsHandler implements MessageHandlerInterface
{
    public function __construct(
        private NewsStatisticsService $newsStatisticsService,
    ) {
    }

    public function __invoke(SendWeeklyStatsMessage $message): void
    {
        $this->newsStatisticsService->sendStats($message->dateFrom, $message->dateTo);
    }
}
