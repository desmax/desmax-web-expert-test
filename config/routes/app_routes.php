<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;
use App\Infra\Controller\FeedController;
use App\Infra\Controller\LoginController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('app_feed', '/')
        ->controller(FeedController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_category_show', '/category/{id}')
        ->controller(FeedController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_news_show', '/news/{id}')
        ->controller(FeedController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_login', '/login')
        ->controller(LoginController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);
};
