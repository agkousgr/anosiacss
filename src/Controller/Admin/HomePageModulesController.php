<?php

namespace App\Controller\Admin;

use App\Entity\HomePageModules;
use App\Entity\HomePageOurCorner;
use App\Entity\Products;
use App\Entity\Slider;
use App\Form\Type\OurCornerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageModulesController extends AbstractController
{
    public function list(EntityManagerInterface $em)
    {
        $modules = $em->getRepository(HomePageModules::class)->findBy([], ['priority' => 'ASC']);
        return $this->render('Admin/homepage_modules/list.html.twig', [
            'modules' => $modules
        ]);
    }

    public function ourCorner(EntityManagerInterface $em)
    {
        $categories = $em->getRepository(HomePageOurCorner::class)->findAll();
        return $this->render('Admin/homepage_modules/our_corner/list.html.twig', [
            'categories' => $categories
        ]);
    }

    public function ourCornerUpdate(Request $request, int $id, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $category = $em->getRepository(HomePageOurCorner::class)->find($id);
            $prevImage = $category->getImage();
            $form = $this->createForm(OurCornerType::class, $category, [
                'action' => $this->generateUrl('our_corner_update', ['id' => $id]),
            ]);
            dump($category);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (empty($form->get('image')->getData())) {
                    $category->setImage($prevImage);
                }
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('our_corner');
            }
            return $this->render('Admin/homepage_modules/our_corner/form.html.twig', [
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

    public function latestOffers(EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $offers = $em->getRepository(Products::class)->getLatestOffers();
            return $this->render('Admin/homepage_modules/latest_offers/list.html.twig', [
                'offers' => $offers
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function latestOfferAdd(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $prId = $request->query->get('id');

            if ($prId) {
                $pr = $em->getRepository(Products::class)->find($prId);

                $pr->setLatestOffer();
                $em->persist($pr);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('latest_offers');
            }
            return $this->render('Admin/homepage_modules/latest_offers/form.html.twig');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function latestOfferUpdate(EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $offers = $em->getRepository(Products::class)->getLatestOffers();
            return $this->render('Admin/homepage_modules/latest_offers/list.html.twig', [
                'offers' => $offers
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function changePriority(int $id, string $direction, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $module = $em->getRepository(HomePageModules::class)->find($id);
            if ($direction === 'up') {
                $module->setPriority($module->getPriority() - 1);
            } else {
                $module->setPriority($module->getPriority() + 1);
            }
            $em->flush();
            $this->addFlash(
                'success',
                'Η ενημέρωση ολοκληρώθηκε με επιτυχία!'
            );

            return $this->redirectToRoute('home_page_modules');

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->redirectToRoute('home_page_modules');
        }
    }

    public function promoCategoriesList(EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $slides = $em->getRepository(Slider::class)->findBy(['category' => 5], ['priority' => 'ASC']);
            return $this->render('Admin/promo_categories/list.html.twig', [
                'slides' => $slides
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function promoCategoriesCreate(Request $request, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $slider = new Slider();
            $form = $this->createForm(SliderType::class, $slider, [
                'action' => $this->generateUrl('promo_categories_add'),
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $slider->setCategory(5);
                dump($slider);
//
//                $em->persist($slider);
//                $em->flush();
                $this->addFlash(
                    'success',
                    'Η προσθήκη ολοκληρώθηκε με επιτυχία!'
                );
                return $this->redirectToRoute('promo_categories_list');
            }
            return $this->render('Admin/promo_categories/form.html.twig', [
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

    public function promoCategoriesUpdate(Request $request, int $id, EntityManagerInterface $em, LoggerInterface $logger)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
            $prevImage = $slider->getImage();
            $form = $this->createForm(SliderType::class, $slider, [
                'action' => $this->generateUrl('slider_update', ['id' => $id]),
            ]);
            dump($slider);
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
                return $this->redirectToRoute('slider_list');
            }
            dump('wtf');
            return $this->render('Admin/slider/slider_form.html.twig', [
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

    public function promoCategoriesChangePriority(int $id, string $direction, EntityManagerInterface $em, LoggerInterface $logger)
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

            return $this->redirectToRoute('slider_list');

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την εγγραφή! Παρακαλώ δοκιμάστε ξανά.'
            );
            return $this->redirectToRoute('slider_list');
        }
    }

    public function promoCategoriesDelete(Request $request, EntityManagerInterface $em, int $id, LoggerInterface $logger)
    {
        try {
            $slider = $em->getRepository(Slider::class)->find($id);
            if ($request->request->get('delete')) {
                $em->remove($slider);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Η διαγραφή ολοκληρώθηκε με επιτυχία!'
                );
            } else {
                return $this->render('Admin/slider/delete.html.twig', [
                    'slider' => $slider
                ]);
            }
            return $this->redirectToRoute('slider_list');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}