<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\Category;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoriesListController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('admin/category/list.html.twig');
    }
}
