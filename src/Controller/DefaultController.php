<?php

namespace App\Controller;

use Vinkla\Instagram\Instagram;

class DefaultController extends MainController
{
    public function index()
    {
        // Load categories from XML format to array
//        $ctgEntity = $prCategories->InitializeCategories($categoriesXML, $session->get("authID"));
//        $latest = $pr->getItems(-1, $session->get("authID"));

        // Create a new instagram instance.
        $instagram = new Instagram('2209588506.1677ed0.361223b4d3a547eebd1ad92202375d17');
        // Fetch recent user media items.
        $this->instagramfeed = $instagram->media();

        return $this->render('layout.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'totalCartItems' => $this->totalCartItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
            'instagramfeed' => $this->instagramfeed
//            'latest' => $latest
        ]);
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