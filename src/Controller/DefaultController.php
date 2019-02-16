<?php

namespace App\Controller;

use App\Entity\{Article, HomePageOurCorner, Slider};
use Doctrine\ORM\EntityManagerInterface;
use Vinkla\Instagram\Instagram;
use Facebook\Facebook;

class DefaultController extends MainController
{
    public function index(EntityManagerInterface $em)
    {
        try {
// Load categories from XML format to array
//        $ctgEntity = $prCategories->InitializeCategories($categoriesXML, $session->get("authID"));
//        $latest = $pr->getItems(-1, $session->get("authID"));
            $ourCornerProducts = [];
            $ourCorner = $em->getRepository(HomePageOurCorner::class)->findBy(['isPublished' => true]);
            foreach ($ourCorner as $item) {
                $ourCornerProducts[] = [
                  'products' => $this->productService->getRelevantItems(-1, -1, 1, 0, 1, $item->getCategory()->getS1id())
                ];
            }
            dump($ourCornerProducts);
            $bestSellers = $this->productService->getCategoryItems('1578', 0, 9, 'null', 'null', 'null', 1, 'null');
            $latest = $this->productService->getCategoryItems(-1, 0, 9, 'IssueDateAsc', 'null', 'null', 1, 'null');
//            $bestSellers = $this->productService->getCategoryItems('1868', 0, 9, 'null', 'null', 'null', 1, 'null');
            $articles = $em->getRepository(Article::class)->findBy(['category' => 1], ['createdAt' => 'DESC'], 3);
            // Create a new instagram instance.
            $slider = $this->em->getRepository(Slider::class)->getSlider();
            $promoCtgs = $this->em->getRepository(Slider::class)->findBy(['adminCategory' => 5]);

            $instagram = new Instagram('2209588506.1677ed0.361223b4d3a547eebd1ad92202375d17');

            //fb page token
            //EAAImVBEgHtQBAFhb99ycIu6WWZAOdOO3lb0M4M9q4aFOoSCZA4G2fd7W9LpsBZAoCELeykpMST4ZAOmVhygKT13rGRoMd4RXL1lqXGbzds0U1ZB3LTqhuRBMkb3r1pG6lLsaSAwHdMTXTmZB8u0KiKldQmo30Vy3VlJooKKK9agViGBc8zMi8nLW0KKZA9zXCpbfZCcV9sCVgeP7XpZCyZABrC

            // Fetch recent user media items.
            $instagramfeed = $instagram->media();

            $reviews = $this->fb->get('/292956054170320/ratings')->getDecodedBody();
            $fbFeed = $this->fb->get('/292956054170320/feed')->getDecodedBody();
            dump($reviews, $this->fb->get('/292956054170320/feed'));

            return $this->render('layout.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'instagramfeed' => $instagramfeed,
                'slider' => $slider,
                'reviews' => $reviews,
                'homepage' => 1,
                'loginUrl' => $this->loginUrl,
                'articles' => $articles,
                'bestSellers' => $bestSellers,
                'ourCorner' => $ourCorner,
                'ourCornerProducts' => $ourCornerProducts,
                'promoCtgs' => $promoCtgs
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function notFound()
    {
        return $this->render('404/404.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
//            'latest' => $latest
        ]);
    }

//    protected function instragam() {
//        // use this instagram access token generator http://instagram.pixelunion.net/
//        $access_token="2209588506.1677ed0.361223b4d3a547eebd1ad92202375d17";
//        $photo_count=9;
//
//        $json_link="https://api.instagram.com/v1/users/self/media/recent/?";
//        $json_link.="access_token={$access_token}&count={$photo_count}";
//        $json = file_get_contents($json_link);
//        $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
//    }
}