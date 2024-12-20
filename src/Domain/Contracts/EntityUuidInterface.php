<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use Stringable;

interface EntityUuidInterface extends Stringable
{
    public function __construct(string $value);
}
