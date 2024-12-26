<?php

declare(strict_types=1);

namespace App\Infra\Scheduler;

use App\App\News\NewsStatisticsService;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

final readonly class StatsTaskProvider implements ScheduleProviderInterface
{
    public function __construct(private NewsStatisticsService $newsStatisticsService)
    {
    }

    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();

        // TODO change to weekly after debugging
        $schedule->add(RecurringMessage::cron('* * * * *', $this->newsStatisticsService->generateEventForLastWeek()));

        return $schedule;
    }
}
