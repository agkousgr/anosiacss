<?php

namespace App\Controller\Admin;

use App\Entity\HomePageModules;
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