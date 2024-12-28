<?php

declare(strict_types=1);

namespace App\Infra\Command;

use App\Domain\Entity\Category\Category;
use App\Domain\Entity\News\Comment;
use App\Domain\Entity\News\News;
use App\Domain\Entity\User\User;
use App\Infra\Model\CategoryId;
use App\Infra\Model\CommentId;
use App\Infra\Model\NewsId;
use App\Infra\Model\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

use function array_rand;
use function assert;
use function is_string;
use function random_int;

#[AsCommand(
    name: 'app:generate-fake-data',
    description: 'Generates fake data for testing',
)]
class GenerateFakeDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force recreation of data by removing existing entries'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            if ((bool) $input->getOption('force')) {
                $this->clearExistingData($io);
            }

            $this->entityManager->beginTransaction();
            $this->generateData($io);
            $this->entityManager->commit();

            $io->success('Fake data generated successfully');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->rollback();
            }

            $io->error('Failed to generate fake data: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    private function clearExistingData(SymfonyStyle $io): void
    {
        $io->section('Clearing existing data');

        $connection = $this->entityManager->getConnection();

        try {
            // Disable foreign key checks temporarily
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

            $tables = ['comments', 'news_categories', 'news', 'categories', 'users'];
            foreach ($tables as $table) {
                $connection->executeStatement("TRUNCATE TABLE {$table}");
                $io->text("Cleared table: {$table}");
            }
        } finally {
            // Re-enable foreign key checks
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function generateData(SymfonyStyle $io): void
    {
        $faker = Factory::create();

        // Create Users
        $io->section('Creating Users');
        $users = $this->createUsers($io);

        // Create Categories
        $io->section('Creating Categories');
        $categories = $this->createCategories($io);

        // Create News and Comments
        $io->section('Creating News Articles and Comments');
        $this->createNewsAndComments($io, $faker, $users, $categories);
    }

    /** @return array<User> */
    private function createUsers(SymfonyStyle $io): array
    {
        // Create admin user
        $adminUser = new User(new UserId(), 'info@lasprasoft.com');
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'asdfgh'));
        $this->entityManager->persist($adminUser);
        $io->text('Created admin user: info@lasprasoft.com');

        // Create regular users
        $users = [$adminUser];
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $email = 'user' . $i . '@example.com';
            $user  = new User(new UserId(), $email);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $this->entityManager->persist($user);
            $users[] = $user;
            $io->text('Created regular user: ' . $email);
        }

        return $users;
    }

    /** @return array<Category> */
    private function createCategories(SymfonyStyle $io): array
    {
        $categoryTitles = ['Technology', 'Business', 'Science', 'Health'];
        $categories     = [];

        foreach ($categoryTitles as $title) {
            $category = new Category(new CategoryId(), $title);
            $this->entityManager->persist($category);
            $categories[] = $category;
            $io->text('Created category: ' . $title);
        }

        return $categories;
    }

    /**
     * @param array<User>     $users
     * @param array<Category> $categories
     */
    private function createNewsAndComments(
        SymfonyStyle $io,
        Generator $faker,
        array $users,
        array $categories,
    ): void {
        $newsCount    = 0;
        $commentCount = 0;

        foreach ($categories as $category) {
            for ($i = 0; $i < 40; $i++) {
                $content = $faker->paragraphs(3, true);
                assert(is_string($content));

                $news = new News(
                    new NewsId(),
                    $users[array_rand($users)],
                    $faker->sentence(6),
                    $faker->paragraph(),
                    $content,
                );

                $news->addCategory($category);
                $this->entityManager->persist($news);
                $newsCount++;

                // Add random number of comments (0-30) for each news
                $numComments = random_int(0, 30);
                for ($j = 0; $j < $numComments; $j++) {
                    $comment = new Comment(
                        new CommentId(),
                        $news,
                        $users[array_rand($users)],
                        $faker->paragraph()
                    );
                    $this->entityManager->persist($comment);
                    $commentCount++;
                }

                // Flush every few entities to avoid memory issues
                if ($i % 5 === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }
        }

        $this->entityManager->flush();
        $io->text("Created {$newsCount} news articles and {$commentCount} comments");
    }
}
