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

class BlogController extends MainController
{
    public function listBlog()
    {
        try {
            return $this->render('blog/list.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'loggedUser' => $this->loggedUser,

//                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}