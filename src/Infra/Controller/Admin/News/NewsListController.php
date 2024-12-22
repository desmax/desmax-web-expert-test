<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NewsListController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('admin/news/list.html.twig');
    }
}
