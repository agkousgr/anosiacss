<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Slider;
use App\Form\Type\CategorySliderType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CategorySliderController extends AbstractController
{
    public function list(EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $categories = $em->getRepository(Category::class)->findBy(['isVisible' => true, 'parent' => null], ['priority' => 'ASC']);
            dump($categories);
            return $this->render('Admin/category_slider/list.html.twig', [
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function sliderList(EntityManagerInterface $em, int $id, LoggerInterface $logger)
    {
        try {
            $slides = $em->getRepository(Slider::class)->findBy(
                ['category' => $id],
                ['priority' => 'ASC']);
            $category = $em->getRepository(Category::class)->find($id);
            return $this->render('Admin/category_slider/slider-list.html.twig', [
                'slides' => $slides,
                'category' => $category
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->render('Admin/slider/list.html.twig');
        }
    }

    public function create(Request $request, EntityManagerInterface $em)
    {
        try {
            $id = $request->query->getInt('id');
            $category = $em->getRepository(Category::class)->find($id);
            $slider = new Slider();
            $slider->setCategory($category);
            $form = $this->createForm(CategorySliderType::class, $slider, [
                'action' => $this->generateUrl('category_slider_add'),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
//                $image = $slider->getImage();
//
//                $fileName = $uploader->upload($image);

                // updates the 'image' property to store the Image file name
                // instead of its contents
//                $slider->setImage($fileName);
                $em->persist($slider);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('category_slider_list');
            }
            return $this->render('Admin/category_slider/category_slider_form.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->render('Admin/slider/list.html.twig');
        }
    }

    public function update(Request $request, EntityManagerInterface $em, int $id, LoggerInterface $logger)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
            dump($id);
            $prevImage = $slider->getImage();
            $form = $this->createForm(CategorySliderType::class, $slider, [
                'action' => $this->generateUrl('category_slider_update', ['id' => $id]),
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (empty($form->get('image')->getData())) {
                    $slider->setImage($prevImage);
                }
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('category_slider_list', ['id' => $slider->getCategory()->getId()]);
            }
            return $this->render('Admin/category_slider/category_slider_form.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->redirectToRoute('category_slider_list', ['id' => $slider->getCategory()->getId()]);
        }
    }

    public function changePriority(int $id, string $direction, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
            if ($direction === 'up') {
                $slider->setPriority($slider->getPriority() - 1);
            } else {
                $slider->setPriority($slider->getPriority() + 1);
            }
            $em->flush();
            $this->addFlash(
                'success',
                'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
            );

            return $this->redirectToRoute('category_slider_list', ['id' => $slider->getCategory()->getId()]);

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->redirectToRoute('category_slider_list', ['id' => $id]);
        }
    }
}