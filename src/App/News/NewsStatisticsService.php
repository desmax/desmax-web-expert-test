<?php

declare(strict_types=1);

namespace App\App\News;

use App\App\Scheduler\WeeklyStats\SendWeeklyStatsMessage;
use App\App\Scheduler\WeeklyStats\WeeklyStatsItem;
use DateTimeImmutable;

final readonly class NewsStatisticsService
{
    public function __construct(private NewsRepositoryInterface $newsRepository, private MailService $mailService)
    {
    }

    public function sendStats(DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo): void
    {
        $stats = $this->newsRepository->getTopNewsByComments($dateFrom, $dateTo, 10);

        $data = [];

        foreach ($stats as $stat) {
            $categories = [];
            foreach ($stat['news']->getCategories() as $category) {
                $categories[] = $category->getTitle();
            }

            $data[] = new WeeklyStatsItem(
                $stat['news']->getId()->value,
                $stat['news']->getTitle(),
                $stat['news']->getAuthor()->getEmail(),
                $categories,
                $stat['commentCount'],
            );
        }

        $this->mailService->sendStatsEmail($data, $dateFrom, $dateTo);
    }

    public function generateEventForLastWeek(): SendWeeklyStatsMessage
    {
        return new SendWeeklyStatsMessage(new DateTimeImmutable('-1 week'), new DateTimeImmutable());
    }
}
