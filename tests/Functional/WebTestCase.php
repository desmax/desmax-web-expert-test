<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

use function assert;

abstract class WebTestCase extends BaseWebTestCase
{
    use Database;

    final protected static function getBrowser(): KernelBrowser
    {
        if (! self::$booted) {
            self::bootKernel();
        }

        assert(self::$kernel !== null);

        /** @var KernelBrowser */
        return self::$kernel->getContainer()->get("test.client");
    }
}
