<?php

declare(strict_types=1);

namespace App\Infra\Doctrine\Type;

use App\Infra\Model\NewsId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use RuntimeException;
use Throwable;

use function is_string;

class NewsIdType extends Type
{
    public const string NAME = 'news_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?NewsId
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof NewsId) {
            return $value;
        }

        try {
            if (! is_string($value)) {
                throw new RuntimeException();
            }

            return new NewsId($value);
        } catch (Throwable) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof NewsId) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, self::NAME);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
