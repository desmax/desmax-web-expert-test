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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io    = new SymfonyStyle($input, $output);
        $faker = Factory::create();

        $io->section('Creating Users');

        // Create admin user
        $adminUser = new User(new UserId(), 'info@lasprasoft.com');
        $adminUser->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'info@lasprasoft.com'));
        $this->entityManager->persist($adminUser);

        // Create regular users
        $users = [$adminUser];
        for ($i = 1; $i <= 3; $i++) {
            $user = new User(new UserId(), $faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $this->entityManager->persist($user);
            $users[] = $user;
        }

        $io->section('Creating Categories');

        $categoryTitles = ['Technology', 'Business', 'Science', 'Health'];
        $categories     = [];

        foreach ($categoryTitles as $title) {
            $category = new Category(new CategoryId(), $title);
            $this->entityManager->persist($category);
            $categories[] = $category;
        }

        $io->section('Creating News Articles');

        foreach ($categories as $category) {
            for ($i = 0; $i < 50; $i++) {
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

                // Add random number of comments (0-10) for each news
                $commentCount = random_int(0, 30);
                for ($j = 0; $j < $commentCount; $j++) {
                    $comment = new Comment(
                        new CommentId(),
                        $news,
                        $users[array_rand($users)],
                        $faker->paragraph()
                    );
                    $this->entityManager->persist($comment);
                }
            }
        }

        $this->entityManager->flush();

        $io->success('Fake data generated successfully');

        return Command::SUCCESS;
    }
}
