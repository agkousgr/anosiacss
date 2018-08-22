<?php

namespace App\Controller\Admin;

use App\Entity\AdminCategory;
use App\Entity\Article;
use App\Form\Type\AdminCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends AbstractController
{
    public function list(EntityManagerInterface $em)
    {
        $articles = $em->getRepository(Article::class)->findAll();
        dump($articles);
        return $this->render('Admin/articles/list.html.twig', [
            'articles' => $articles
        ]);
    }

    public function create(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $article = new Article();
            $form = $this->createForm(ArticleType::class, $article, [
                'action' => $this->generateUrl('category_add'),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($article);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('article_list');

            }
            return $this->render('Admin/articles/article_form.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(Request $request, int $id, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $article = $em->getRepository(Article::class)->find($id);
            dump($article);
            $form = $this->createForm(ArticleType::class, $article, [
                'action' => $this->generateUrl('article_update', ['id' => $id]),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // updates the 'image' property to store the Image file name
                // instead of its contents

                $em->flush();
                $this->addFlash(
                    'success',
                    'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('article_list');

            }
            return $this->render('Admin/articles/article_form.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(Request $request, EntityManagerInterface $em, int $id, LoggerInterface $logger)
    {
        try {
            $article = $em->getRepository(Article::class)->find($id);
            if ($request->request->get('delete')) {
                $em->remove($article);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η διαγραφή ολοκληρώθηκε με επιτυχία!'
                );
            }else{
                return $this->render('Admin/articles/delete.html.twig', [
                    'category' => $article
                ]);
            }
            return $this->redirectToRoute('article_list');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}