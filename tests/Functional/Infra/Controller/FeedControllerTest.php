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
use Symfony\Component\DomCrawler\Crawler;

class FeedControllerTest extends WebTestCase
{
    public function testFeed(): void
    {
        $client = self::getBrowser();

        self::dbTransaction(static function (EntityManagerInterface $em): void {
            $user = new User(new UserId(null), 'random@gmail.com');
            $em->persist($user);

            $category1 = new Category(new CategoryId(null), 'randomCategory1');
            $em->persist($category1);

            $category2 = new Category(new CategoryId(null), 'randomCategory2');
            $em->persist($category2);

            $category3 = new Category(new CategoryId(null), 'randomCategory3');
            $em->persist($category3);

            $category4 = new Category(new CategoryId(null), 'randomCategory4');
            $em->persist($category4);

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

            $news4 = new News(new NewsId(null), $user, 'randomNews4', 'randomNews4Content', 'randomNews4Content');
            $news4->addCategory($category1);
            $em->persist($news4);

            $news5 = new News(new NewsId(null), $user, 'randomNews5', 'randomNews5Content', 'randomNews5Content');
            $news5->addCategory($category1);
            $em->persist($news5);

            $news6 = new News(new NewsId(null), $user, 'randomNews6', 'randomNews6Content', 'randomNews6Content');
            $news6->addCategory($category1);
            $em->persist($news6);
        });

        /** @var Crawler $crawler */
        $crawler = $client->request('GET', '/');

        // Verify all categories are present
        $categoryCards = $crawler->filter('.category-card');
        self::assertCount(3, $categoryCards);

        // Verify category titles
        $categoryTitles = $crawler->filter('.category-card h2')->extract(['_text']);
        self::assertContains('randomCategory1', $categoryTitles);
        self::assertContains('randomCategory2', $categoryTitles);
        self::assertContains('randomCategory3', $categoryTitles);

        // Verify news distribution per category
        $categoryCards->each(function (Crawler $categoryCard) {
            $categoryTitle = $categoryCard->filter('h2')->text();
            $newsCards = $categoryCard->filter('.news-card');

            switch ($categoryTitle) {
                case 'randomCategory1':
                    self::assertCount(4, $newsCards, 'Category 1 should have 3 news items');
                    self::assertContains('randomNews4', $this->extractNewsTitle($newsCards));
                    self::assertContains('randomNews5', $this->extractNewsTitle($newsCards));
                    self::assertContains('randomNews6', $this->extractNewsTitle($newsCards));
                    break;

                case 'randomCategory2':
                    self::assertCount(2, $newsCards, 'Category 2 should have 2 news items');
                    self::assertContains('randomNews1', $this->extractNewsTitle($newsCards));
                    self::assertContains('randomNews2', $this->extractNewsTitle($newsCards));
                    break;

                case 'randomCategory3':
                    self::assertCount(2, $newsCards, 'Category 3 should have 2 news items');
                    self::assertContains('randomNews2', $this->extractNewsTitle($newsCards));
                    self::assertContains('randomNews3', $this->extractNewsTitle($newsCards));
                    break;
                default:
                    self::fail('Unexpected category title');
            }

            // Verify each news card structure
            $newsCards->each(function (Crawler $newsCard) {
                self::assertNotEmpty($newsCard->filter('h3')->text(), 'News title should not be empty');
                self::assertNotEmpty($newsCard->filter('p')->text(), 'News description should not be empty');
                self::assertCount(1, $newsCard->filter('.btn'), 'Each news card should have a read more button');
            });

            // Verify category has a "View all news" button
            self::assertSame('View all news', $categoryCard->filter('.btn')->last()->text(), 'Category should have a view all button');
        });
    }

    private function extractNewsTitle(Crawler $newsCards): array
    {
        return $newsCards->filter('h3')->extract(['_text']);
    }
}
