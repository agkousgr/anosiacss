<?php

namespace App\Controller\Admin;

use App\Entity\HomePageModules;
use App\Entity\HomePageOurCorner;
use App\Entity\Products;
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

    public function latestOffersUpdate(EntityManagerInterface $em, LoggerInterface $logger)
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
}