<?php

declare(strict_types=1);

namespace App\App\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFound extends Exception implements NotFoundExceptionInterface
{
}
