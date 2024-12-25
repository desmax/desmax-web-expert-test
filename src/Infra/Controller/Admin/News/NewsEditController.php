<?php

declare(strict_types=1);

namespace App\Infra\Controller\Admin\News;

use App\Domain\Entity\News\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

use function file_exists;
use function unlink;

class NewsEditController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function __invoke(News $news, Request $request): Response
    {
        $dto  = NewsDTO::fromNews($news);
        $form = $this->createForm(NewsType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dto = $form->getData();

            $oldPicture = $news->getPicture();

            if ($dto->picture instanceof UploadedFile) {
                $fileName = Uuid::v7() . '.' . $dto->picture->getClientOriginalExtension();
                $dto->picture->move($this->getParameter('news_images_directory'), $fileName);
                $news->setPicture($fileName);
            }

            $news->setTitle($dto->title);
            $news->setShortDescription($dto->shortDescription);
            $news->setContent($dto->content);

            // Reset categories and add new ones
            foreach ($news->getCategories() as $category) {
                $news->removeCategory($category);
            }

            foreach ($dto->categories as $category) {
                $news->addCategory($category);
            }

            $this->em->flush();

            // Remove old picture
            if ($oldPicture !== null && file_exists($this->getParameter('news_images_directory') . '/' . $oldPicture)) {
                unlink($this->getParameter('news_images_directory') . '/' . $oldPicture);
            }

            $this->addFlash('success', 'News updated successfully');

            return $this->redirectToRoute('app_admin_news_list');
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }
}
