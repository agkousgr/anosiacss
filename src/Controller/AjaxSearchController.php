<?php


namespace App\Controller;


use App\Entity\AnosiaSearchKeywords;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AjaxSearchController extends AbstractController
{
    public function search(Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $resultArr = [];
                $keyword = $request->query->get('term');
//                $result = $em->getRepository(AnosiaSearchKeywords::class)->getAnosiaSearchResult($keyword);
                $result = $em->getRepository(Products::class)->search($keyword);
                dump($result);
                foreach ($result as $val) {
                    $resultArr[] = [
                        'value' => $val['id'],
                        'label' => $val['product_name'] . ' (Κωδ: ' . $val['pr_code'] .')'
                    ];
                }
                return $this->json($resultArr);

            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function searchForAnosia(Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $resultArr = [];
                $keyword = $request->query->get('term');
                $result = $em->getRepository(AnosiaSearchKeywords::class)->getAnosiaSearchResult($keyword);
                foreach ($result as $val) {
                    $resultArr[] = [
                        'value' => $val->getSlug(),
                        'label' => $val->getKeyword()
                    ];
                }
                return $this->json($resultArr);

            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function redirectToCategory(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $category = $request->request->get('category');
                return $this->redirectToRoute('products_list', ['id' => $category]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}