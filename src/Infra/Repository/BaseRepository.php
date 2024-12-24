<?php declare(strict_types=1);

namespace App\Infra\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function find($id, $lockMode = null, int|null $lockVersion = null): null|object
    {
        if (is_string($id)) {
            $id = $this->convertStringToEntityId($id);
        }

        return parent::find($id, $lockMode, $lockVersion);
    }

    abstract protected function convertStringToEntityId(string $id): object;
}
