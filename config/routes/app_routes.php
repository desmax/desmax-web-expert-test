<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;
use App\Infra\Controller\FeedController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('feed', '/')
        ->controller(FeedController::class)
        ->methods([Request::METHOD_GET])
    ;
};
