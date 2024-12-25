<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpFoundation\Request;
use App\Infra\Controller\Admin\DashboardController;
use App\Infra\Controller\Admin\Category\CategoriesListController;
use App\Infra\Controller\Admin\Category\CategoriesCreateController;
use App\Infra\Controller\Admin\Category\CategoriesEditController;
use App\Infra\Controller\Admin\Category\CategoriesArchiveController;
use App\Infra\Controller\Admin\News\NewsListController;
use App\Infra\Controller\Admin\News\NewsCreateController;
use App\Infra\Controller\Admin\News\NewsEditController;

return static function (RoutingConfigurator $routes): void {
    $routes->add('app_admin_dashboard', '/admin')
        ->controller(DashboardController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_admin_category_list', '/admin/categories')
        ->controller(CategoriesListController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_admin_category_create', '/admin/categories/create')
        ->controller(CategoriesCreateController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    $routes->add('app_admin_category_edit', '/admin/categories/{id}/edit')
        ->controller(CategoriesEditController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    $routes->add('app_admin_category_archive', '/admin/categories/{id}/archive')
        ->controller(CategoriesArchiveController::class)
        ->methods([Request::METHOD_POST]);

    $routes->add('app_admin_news_list', '/admin/news')
        ->controller(NewsListController::class)
        ->methods([Request::METHOD_GET]);

    $routes->add('app_admin_news_create', '/admin/news/create')
        ->controller(NewsCreateController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    $routes->add('app_admin_news_edit', '/admin/news/{id}/edit')
        ->controller(NewsEditController::class)
        ->methods([Request::METHOD_GET, Request::METHOD_POST]);

    $routes->add('app_admin_news_archive', '/admin/news/{id}/archive')
        ->controller(NewsCreateController::class)
        ->methods([Request::METHOD_POST]);
};
