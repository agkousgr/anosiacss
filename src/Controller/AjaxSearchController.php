<?php


namespace App\Controller;


use App\Entity\AnosiaSearchKeywords;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AjaxSearchController extends AbstractController
{
    public function searchForAnosia(Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $resultArr = [];
                $keyword = $request->query->get('term');
                $result = $em->getRepository(AnosiaSearchKeywords::class)->getAnosiaSearchResult($keyword);
                foreach ($result as $val) {
                    $resultArr[] = [
                        'value' => $val->getCategoryId(),
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