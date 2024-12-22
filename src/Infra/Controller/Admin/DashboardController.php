<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
