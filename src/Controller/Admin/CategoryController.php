<?php

namespace App\Controller\Admin;

use App\Entity\AdminCategory;
use App\Form\Type\AdminCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    public function list(EntityManagerInterface $em)
    {
//        $category = new BlogCategory();
        $categories = $em->getRepository(AdminCategory::class)->findBy(['parent' => null], ['priority' => 'ASC']);

        return $this->render('Admin/categories/list.html.twig', [
            'categories' => $categories
        ]);
    }

    public function create(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $category = new AdminCategory();
            $form = $this->createForm(AdminCategoryType::class, $category, [
                'action' => $this->generateUrl('category_add'),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em->persist($category);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('category_list');

            }
            return $this->render('Admin/categories/category.html.twig', [
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
            $category = $em->getRepository(AdminCategory::class)->find($id);

            $form = $this->createForm(AdminCategoryType::class, $category, [
                'action' => $this->generateUrl('category_update', ['id' => $id]),
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
                return $this->redirectToRoute('category_list');

            }
            return $this->render('Admin/categories/category.html.twig', [
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
            $category = $em->getRepository(AdminCategory::class)->find($id);
            if ($request->request->get('delete')) {
                $em->remove($category);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η διαγραφή ολοκληρώθηκε με επιτυχία!'
                );
            }else{
                return $this->render('Admin/categories/delete.html.twig', [
                    'category' => $category
                ]);
            }
            return $this->redirectToRoute('category_list');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}