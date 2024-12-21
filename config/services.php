<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use App\Infra\FixPostgreSQLDefaultSchemaListener;
use Doctrine\ORM\Tools\ToolEvents;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', '../src/')
        ->exclude([
            '../src/Domain/',
            '../src/Kernel.php'
        ]);

    $services
        ->set(FixPostgreSQLDefaultSchemaListener::class)
        ->tag('doctrine.event_listener', ['event' => ToolEvents::postGenerateSchema]);
};
