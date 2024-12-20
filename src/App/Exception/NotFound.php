<?php

declare(strict_types=1);

namespace App\Application\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFound extends Exception implements NotFoundExceptionInterface
{
}
