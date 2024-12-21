<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\After;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

use function assert;

trait Database
{
    private static bool $dbInitialized = false;

    final protected static function getManagerRegistry(): ManagerRegistry
    {
        $registry = self::getContainer()->get(ManagerRegistry::class);

        if (self::$dbInitialized) {
            return $registry;
        }

        self::$dbInitialized = true;

        $application = new Application(self::getContainer()->get(KernelInterface::class));
        $application->setAutoExit(false);

        $commands = [
            [
                'command' => 'doctrine:database:drop',
                '--force' => true,
                '--env' => 'test',
            ],
            [
                'command' => 'doctrine:database:create',
                '--env' => 'test',
            ],
            [
                'command' => 'doctrine:schema:create',
                '--env' => 'test',
            ],
        ];

        foreach ($commands as $command) {
            $application->run(new ArrayInput($command), new NullOutput());
        }

        return $registry;
    }

    #[After]
    public function teardownDatabase(): void
    {
        self::$dbInitialized = false;
    }

    final protected static function getDefaultEntityManager(): EntityManagerInterface
    {
        $em = self::getManagerRegistry()->getManager();
        assert($em instanceof EntityManagerInterface);

        return $em;
    }

    /** @param callable(EntityManagerInterface): void $func */
    final protected static function dbTransaction(callable $func): void
    {
        $em = self::getDefaultEntityManager();

        $em->wrapInTransaction($func);
        $em->clear();
    }
}
