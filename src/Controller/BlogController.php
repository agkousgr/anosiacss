<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/7/2018
 * Time: 5:32 μμ
 */

namespace App\Controller;

use App\Entity\Article;
use App\Service\{
    ProductService, SoftoneLogin, CategoryService
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends MainController
{
    public function listBlog(EntityManagerInterface $em)
    {
        try {
            $articles = $em->getRepository(Article::class)->findBy(['category' => 1], ['createdAt' => 'DESC']);
            return $this->render('blog/list.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'articles' => $articles
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewBlog(EntityManagerInterface $em, string $slug)
    {
        try {
            $article = $em->getRepository(Article::class)->findOneBy(['slug' => $slug]);
            $article->setViews($article->getViews() + 1);
            $em->flush();
            $popularArticles = $em->getRepository(Article::class)->findBy(
                ['category' => 1],
                ['views' => 'DESC'],
                4);
            return $this->render('blog/view.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'article' => $article,
                'popularArticles' => $popularArticles
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}