<?php

declare(strict_types=1);

namespace App\App\News;

use App\App\Mail\EmailSenderInterface;
use App\App\Scheduler\WeeklyStats\WeeklyStatsItem;
use DateTimeImmutable;

use function sprintf;

final readonly class MailService
{
    public function __construct(private EmailSenderInterface $mailer, private string $statsEmailRecipient)
    {
    }

    /** @param WeeklyStatsItem[] $stats */
    public function sendStatsEmail(array $stats, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo): void
    {
        $this->mailer->sendEmail(
            $this->statsEmailRecipient,
            sprintf(
                'Weekly News Statistics (%s - %s)',
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d')
            ),
            'emails/weekly_stats.html.twig',
            [
                'stats' => $stats,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]
        );
    }
}
