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
            $categories = $em->getRepository(Category::class)->findBy(['isVisible' => true], ['priority' => 'ASC']);
            dump($categories);
            return $this->render('Admin/category_slider/list.html.twig', [
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function sliderList()
    {
        return $this->render('Admin/category_slider/slider-list.html.twig');
    }

    public function create(Request $request, EntityManagerInterface $em, FileUploader $uploader)
    {
        try {
            dump($request);
            $slider = new Slider();
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
                return $this->redirectToRoute('slider_list');
            }
            return $this->render('Admin/category_slider/category_slider.html.twig', [
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

    public function update(Request $request, int $id, EntityManagerInterface $em, FileUploader $uploader)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
            $em->getRepository()->find($id);
            $form = $this->createForm(CategorySliderType::class, $slider);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $image = $slider->getImage();

                $fileName = $uploader->upload($image);

                // updates the 'image' property to store the Image file name
                // instead of its contents
                $slider->setImage($fileName);
                $em->persist($slider);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
                );
            }
            return $this->render('Admin/slider/slider.html.twig', [
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
}