<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/7/2018
 * Time: 5:32 μμ
 */

namespace App\Controller;

use App\Service\{
    ProductService, SoftoneLogin, CategoryService
};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    public function listBlog(SoftoneLogin $softoneLogin, CategoryService $categoryService, ProductService $productService, LoggerInterface $logger, SessionInterface $session)
    {
        try {
            $softoneLogin->login();
            $categories = $categoryService->getCategories();
            array_multisort(array_column($categories, "priority"), $categories);
            $popular = $productService->getCategoryItems(1022, $session->get("authID"));
            $loggedUser = (null !== $session->get("anosiaUser")) ?: null;
//            $pagination = $knp->paginate(
//                $products,
//                $request->query->getInt('page', 1)/*page number*/,
//                10/*limit per page*/
//            );
            return $this->render('blog/list.html.twig', [
                'categories' => $categories,
                'popular' => $popular,
                'loggedUser' => $loggedUser
//                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}