<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use App\Infra\Doctrine\FixPostgreSQLDefaultSchemaListener;
use Doctrine\ORM\Tools\ToolEvents;
use App\App\Scheduler\MessageHandlerInterface;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use App\App\News\MailService;
use App\Infra\Mail\EmailSender;

return static function (ContainerConfigurator $configurator): void {
    $parameters = $configurator->parameters();
    $parameters->set('news_images_directory', '%kernel.project_dir%/public/uploads/news');

    $services = $configurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->instanceof(MessageHandlerInterface::class)
        ->tag('messenger.message_handler');

    $services->instanceof(ScheduleProviderInterface::class)
        ->tag('scheduler.schedule_provider', ['name' => 'cron']);

    $services->load('App\\', '../src/')
        ->exclude([
            '../src/Domain/',
            '../src/Kernel.php'
        ]);

    $services
        ->set(MailService::class)
        ->arg('$statsEmailRecipient', '%env(NEWS_STATS_EMAIL)%');

    $services
        ->set(EmailSender::class)
        ->arg('$fromEmail', '%env(MAILER_FROM)%');

    $services
        ->set(FixPostgreSQLDefaultSchemaListener::class)
        ->tag('doctrine.event_listener', ['event' => ToolEvents::postGenerateSchema]);
};
