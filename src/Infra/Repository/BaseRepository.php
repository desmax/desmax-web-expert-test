<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;

use function is_string;

/**
 * @template T of object
 * @extends ServiceEntityRepository<T>
 */
abstract class BaseRepository extends ServiceEntityRepository
{
    /** @return T|null */
    public function find($id, LockMode|int|null $lockMode = null, int|null $lockVersion = null): object|null
    {
        if (is_string($id)) {
            $id = $this->convertStringToEntityId($id);
        }

        return parent::find($id, $lockMode, $lockVersion);
    }

    abstract protected function convertStringToEntityId(string $id): object;
}
