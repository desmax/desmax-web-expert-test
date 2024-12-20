<?php

declare(strict_types=1);

namespace App\Infra\Controller;

use Symfony\Component\HttpFoundation\Response;

class FeedController implements ControllerInterface
{
    public function __invoke(): Response
    {
        return new Response('Here will be the news feed');
    }
}
