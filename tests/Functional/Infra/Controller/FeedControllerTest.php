<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infra\Controller;

use App\Domain\Entity\User\User;
use App\Infra\Model\UserId;
use App\Tests\Functional\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class FeedControllerTest extends WebTestCase
{
    public function testFeed(): void
    {
        $browser = static::createClient()->request('GET', '/');

        self::dbTransaction(static function (EntityManagerInterface $em): void {
            $user = new User(new UserId(null), 'random@gmail.com');
            $em->persist($user);
        });

        self::assertStringContainsString('Here will be the news feed', $browser->html());
    }
}
