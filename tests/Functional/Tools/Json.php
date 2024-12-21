<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tools;

use JsonException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

use function assert;
use function is_array;
use function is_object;
use function json_decode;
use function json_encode;

use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

final class Json
{
    /** @param array<mixed>|object|string $data */
    public static function encode(string|array|object $data): string
    {
        return json_encode(value: $data, flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }

    /** @param array<mixed>|object|string $data */
    public static function pretty(string|array|object $data): string
    {
        return json_encode(value: $data, flags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    /**
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public static function decode(string $json): array
    {
        $ret = json_decode(json: $json, associative: true, flags: JSON_THROW_ON_ERROR);
        assert(is_array($ret));

        return $ret;
    }

    public static function decodeToObject(string $json): object
    {
        $ret = json_decode(json: $json, associative: false, flags: JSON_THROW_ON_ERROR);
        assert(is_object($ret));

        return $ret;
    }

    /** @return array<mixed> */
    public static function decodeFromResponse(Response $reposnse): array
    {
        return self::decode(self::getContentFromMessage($reposnse));
    }

    private static function getContentFromMessage(Response $response): string
    {
        $content = $response->getContent();
        if ($content === false) {
            throw new RuntimeException('No content');
        }

        return $content;
    }
}
