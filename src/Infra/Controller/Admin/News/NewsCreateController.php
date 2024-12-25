<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\App\Security\CurrentUserFetcherInterface;
use App\Domain\Entity\News\News;
use App\Infra\Model\NewsId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[IsGranted('ROLE_ADMIN')]
class NewsCreateController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private CurrentUserFetcherInterface $currentUserFetcher)
    {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(NewsType::class, new NewsDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = $form->getData();

            $picture = null;
            if ($dto->picture instanceof UploadedFile) {
                $fileName = Uuid::v7() . '.' . $dto->picture->getClientOriginalExtension();
                $dto->picture->move($this->getParameter('news_images_directory'), $fileName);
                $picture = $fileName;
            }

            $news = new News(
                new NewsId(),
                $this->currentUserFetcher->getCurrentUser(),
                $dto->title,
                $dto->shortDescription,
                $dto->content,
            );

            $news->setPicture($picture);

            foreach ($dto->categories as $category) {
                $news->addCategory($category);
            }

            $this->em->persist($news);
            $this->em->flush();

            $this->addFlash('success', 'News created successfully');

            return $this->redirectToRoute('app_admin_news_list');
        }

        return $this->render('admin/news/create.html.twig', ['form' => $form]);
    }
}
