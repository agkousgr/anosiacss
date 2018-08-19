<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 9/8/2018
 * Time: 12:19 πμ
 */

namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function dashboard()
    {
        return $this->render('Admin/layout.html.twig');
    }
}