<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

abstract class EntityUuid implements EntityUuidInterface
{
    public function __construct(
        public string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
