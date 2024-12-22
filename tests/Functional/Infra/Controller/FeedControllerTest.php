<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infra\Controller;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\News\News;
use App\Domain\Entity\User\User;
use App\Infra\Model\CategoryId;
use App\Infra\Model\NewsId;
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

            $category1 = new Category(new CategoryId(null), 'randomCategory1');
            $em->persist($category1);

            $category2 = new Category(new CategoryId(null), 'randomCategory2');
            $em->persist($category2);

            $category3 = new Category(new CategoryId(null), 'randomCategory3');
            $em->persist($category3);

            $news1 = new News(new NewsId(null), $user, 'randomNews1', 'randomNews1Content', 'randomNews1Content');
            $news1->addCategory($category1);
            $news1->addCategory($category2);
            $em->persist($news1);

            $news2 = new News(new NewsId(null), $user, 'randomNews2', 'randomNews2Content', 'randomNews2Content');
            $news2->addCategory($category2);
            $news2->addCategory($category3);
            $em->persist($news2);

            $news3 = new News(new NewsId(null), $user, 'randomNews3', 'randomNews3Content', 'randomNews3Content');
            $news3->addCategory($category3);
            $em->persist($news3);
        });

        self::assertStringContainsString('Here will be the news feed', $browser->html());
    }
}
