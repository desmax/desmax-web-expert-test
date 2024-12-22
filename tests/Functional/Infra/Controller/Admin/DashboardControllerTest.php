<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infra\Controller\Admin;

use App\App\User\UserRepositoryInterface;
use App\Domain\Entity\User\User;
use App\Infra\Model\UserId;
use App\Tests\Functional\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DashboardControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::dbTransaction(static function (EntityManagerInterface $em): void {
            $user = new User(new UserId(null), 'user@gmail.com');
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);

            $admin = new User(new UserId(null), 'admin@gmail.com');
            $admin->setRoles(['ROLE_ADMIN']);
            $em->persist($admin);
        });
    }

    public function testThatNonLoggedInUserCannotAccessAdminDashboard(): void
    {
        $browser = self::getBrowser();
        $browser->followRedirects();

        $browser->request('GET', '/admin');

        // Check if the request is redirected to login page
        self::assertSame(Response::HTTP_OK, $browser->getResponse()->getStatusCode());

        $responseContent = $browser->getResponse()->getContent();
        self::assertIsString($responseContent);
        self::assertStringContainsString('Login', $responseContent);
    }

    public function testNonAdminUserCannotAccessAdminDashboard(): void
    {
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $browser = self::getBrowser();

        $user = $userRepository->getByEmail('user@gmail.com');
        $browser->loginUser($user);

        $browser->request('GET', '/admin');
        self::assertSame(Response::HTTP_FORBIDDEN, $browser->getResponse()->getStatusCode());
    }

    public function testAdminCanAccessAdminDashboard(): void
    {
        $userRepository = self::getContainer()->get(UserRepositoryInterface::class);

        $browser = self::getBrowser();

        $admin = $userRepository->getByEmail('admin@gmail.com');
        $browser->loginUser($admin);

        $crawler = $browser->request('GET', '/admin');
        self::assertSame(Response::HTTP_OK, $browser->getResponse()->getStatusCode());

        self::assertStringContainsString('Here will be the dashboard!', $crawler->html());
    }
}
