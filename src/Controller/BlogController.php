<?php

namespace App\Controller;

use App\Entity\AdminCategory;
use App\Entity\Article;
use App\Entity\User;
use App\Service\{
    ProductService, SoftoneLogin
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends MainController
{
    public function listBlog(EntityManagerInterface $em, int $page)
    {
        try {
            $pagesize = 16;
            $articles = $em->getRepository(Article::class)->findBy(
                ['category' => 1, 'isPublished' => 1],
                ['createdAt' => 'DESC'],
                $pagesize,
                ($page-1) * $pagesize);
            $blogCount = $em->getRepository(Article::class)->count(['isPublished' => 1]);
            $popularArticles = $em->getRepository(Article::class)->findBy(
                ['category' => 1],
                ['views' => 'DESC'],
                4);
            return $this->render('blog/list.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'articles' => $articles,
                'popularArticles' => $popularArticles,
                'loginUrl' => $this->loginUrl,
                'pagesize' => $pagesize,
                'blogCount' => $blogCount,
                'page' => $page
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
            $user = $em->getRepository(User::class)->find(1);
            $article->setUpdatedBy($user);
            $em->flush();
            $popularArticles = $em->getRepository(Article::class)->findBy(
                ['category' => 1],
                ['views' => 'DESC'],
                4);
            return $this->render('blog/view.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'article' => $article,
                'popularArticles' => $popularArticles,
                'loginUrl' => $this->loginUrl
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}