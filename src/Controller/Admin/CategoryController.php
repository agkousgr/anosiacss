<?php

namespace App\Controller\Admin;

use App\Entity\BlogCategory;
use App\Form\Type\BlogCategoryType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
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

    public function create(Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $category = new BlogCategory();
                $form = $this->createForm(BlogCategoryType::class, $category);
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
                throw $this->createNotFoundException('The resource you are looking for could not be found.' . $e);
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}