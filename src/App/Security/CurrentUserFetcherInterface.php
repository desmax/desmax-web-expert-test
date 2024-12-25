<?php

declare(strict_types=1);

namespace App\App\Security;

use App\Domain\Entity\User\User;

interface CurrentUserFetcherInterface
{
    public function getCurrentUser(): User;
}
