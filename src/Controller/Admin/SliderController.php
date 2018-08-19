<?php

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Form\Type\SliderType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

class SliderController extends AbstractController
{
    public function list(EntityManagerInterface $em)
    {
        $slides = $em->getRepository(Slider::class)->findBy(['category' => null],['priority' => 'ASC']);
        return $this->render('Admin/slider/list.html.twig', [
            'slides' => $slides
        ]);
    }

    public function create(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $slider = new Slider();
            $form = $this->createForm(SliderType::class, $slider, [
                'action' => $this->generateUrl('slider_add'),
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
//                $slider->setImage($form->get('image')->getData());
                $em->persist($slider);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('slider_list');
            }
            return $this->render('Admin/slider/slider.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
//            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
//            throw $e;
            return $this->redirectToRoute('slider_list');
        }
    }

    public function update(Request $request, int $id, EntityManagerInterface $em, FileUploader $uploader, LoggerInterface $logger)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
//            $slider->setImage(new File($uploader->getTargetDirectory().'/'.$slider->getImage()));
            $form = $this->createForm(SliderType::class, $slider, [
                'action' => $this->generateUrl('slider_update', ['id' => $id]),
            ]);
            dump('zong');
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('slider_list');
            }
            dump('wtf');
            return $this->render('Admin/slider/slider.html.twig', [
                'form' => $form->createView()
            ]);

        } catch (\Exception $e) {
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            return $this->redirectToRoute('slider_list');
        }
    }
}