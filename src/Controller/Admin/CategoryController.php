<?php

namespace App\Controller\Admin;

use App\Entity\BlogCategory;
use App\Form\Type\BlogCategoryType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    public function list(Request $request, EntityManagerInterface $em)
    {
//        $category = new BlogCategory();
        $categories = $em->getRepository(BlogCategory::class)->findBy([], ['priority' => 'ASC']);
        dump($categories);
        return $this->render('Admin/categories/list.html.twig', [
            'categories' => $categories
        ]);
    }

    public function create(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $category = new BlogCategory();
            dump($request);
            $form = $this->createForm(BlogCategoryType::class, $category, [
                'action' => $this->generateUrl('category_add'),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // updates the 'image' property to store the Image file name
                // instead of its contents
                $em->persist($category);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );

            }
            return $this->render('Admin/categories/create.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}