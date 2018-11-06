<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AjaxSearchController extends AbstractController
{
    public function searchForAnosia(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $keyword = $request->request->get('keyword');


            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}