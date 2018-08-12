<?php

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Form\Type\SliderType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends AbstractController
{
    public function list()
    {
        return $this->render('Admin/slider/list.html.twig');
    }

    public function create(Request $request, EntityManagerInterface $em, FileUploader $uploader)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $slider = new Slider();
                $form = $this->createForm(SliderType::class, $slider);
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
                        'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                    );
                }
                return $this->render('Admin/slider/create.html.twig', [
                    'form' => $form->createView()
                ]);

            } catch (\Exception $e) {
                $this->addFlash(
                    'notice',
                    'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
                );
                return $this->render('Admin/slider/list.html.twig');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function update(Request $request, EntityManagerInterface $em, FileUploader $uploader)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $slider = new Slider();
                $id = $request->query->getInt('id');
                $em->getRepository()->find($id);
                $form = $this->createForm(SliderType::class, $slider);
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
                        'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                    );
                }
                return $this->render('Admin/slider/create.html.twig', [
                    'form' => $form->createView()
                ]);

            } catch (\Exception $e) {
                $this->addFlash(
                    'notice',
                    'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
                );
                return $this->render('Admin/slider/list.html.twig');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}