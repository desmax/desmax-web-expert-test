<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->defaults()
        ->autowire(true)
        ->autoconfigure(true);

    $services->load('App\\', '../src/')
        ->exclude([
            '../src/Domain/',
            '../src/Kernel.php'
        ]);
};
