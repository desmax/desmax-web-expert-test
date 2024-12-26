<?php

declare(strict_types=1);

namespace App\App\Mail;

interface EmailSenderInterface
{
    /** @param array<string, mixed> $context */
    public function sendEmail(string $to, string $subject, string $template, array $context): void;
}
