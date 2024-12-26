<?php

declare(strict_types=1);

namespace App\Infra\Mail;

use App\App\Mail\EmailSenderInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailSender implements EmailSenderInterface
{
    public function __construct(private MailerInterface $mailer, private string $fromEmail)
    {
    }

    public function sendEmail(string $to, string $subject, string $template, array $context): void
    {
        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailer->send($email);
    }
}
