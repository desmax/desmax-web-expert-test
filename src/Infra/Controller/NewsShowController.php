<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use App\Domain\Entity\News\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class NewsShowController extends AbstractController
{
    public function __invoke(News $news): Response
    {
        return $this->render('news/show.html.twig', ['news' => $news]);
    }
}
