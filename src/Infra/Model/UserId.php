<?php declare(strict_types=1);

namespace App\Infra\Model;

use Symfony\Component\Uid\Uuid;
use App\Domain\Model\UserId as BaseUserId;

class UserId extends BaseUserId {
    public function __construct(
        ?string $value = null,
    ) {
        parent::__construct($value ?? Uuid::v7()->toRfc4122());
    }
}
