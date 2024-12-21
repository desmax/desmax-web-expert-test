<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\App\Exception\NotFound;
use App\App\User\UserRepositoryInterface;
use App\Domain\Entity\User\User;
use App\Domain\Model\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

use function sprintf;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<PasswordAuthenticatedUserInterface>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getById(UserId $id): User
    {
        $user = $this->find($id);

        if ($user === null) {
            throw new NotFound(sprintf('User with ID "%s" not found.', $id));
        }

        return $user;
    }
}
