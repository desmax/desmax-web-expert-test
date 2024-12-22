<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FeedController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('feed/index.html.twig');
    }
}
