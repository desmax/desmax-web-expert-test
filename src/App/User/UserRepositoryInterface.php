<?php

declare(strict_types=1);

namespace App\App\User;

use App\Domain\Entity\User\User;
use App\Domain\Model\UserId;

interface UserRepositoryInterface
{
    public function getById(UserId $id): User;

    public function getByEmail(string $email): User;
}
