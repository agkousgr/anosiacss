<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 19/8/2018
 * Time: 5:38 μμ
 */

namespace App\Controller\Admin;


use App\Entity\HomePageModules;
use Doctrine\ORM\EntityManagerInterface;
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

}