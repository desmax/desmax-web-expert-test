<?php

declare(strict_types=1);

namespace App\Infra\Security;

use App\App\Security\CurrentUserFetcherInterface;
use App\Domain\Entity\User\User;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class CurrentUserFetcher implements CurrentUserFetcherInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function getCurrentUser(): User
    {
        $user = $this->tokenStorage->getToken()?->getUser();
        if (! $user instanceof User) {
            throw new RuntimeException('User not found or wrong type');
        }

        return $user;
    }
}
