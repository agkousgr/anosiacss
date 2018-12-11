<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DomCrawler;

class CourierTrackerController extends MainController
{
    public function trackVoucher(Request $request, string $voucher)
    {
        try {
            $courierHtml = '';
//            $voucher = $request->query->get('');
//            $page = file_get_contents('http://speedex.gr/isapohi.asp?voucher_code=' . $voucher . '&searcggo=Submit');
//            $doc = new \DOMDocument();
//            @$doc->loadHTML($page);
//            $divs = $doc->getElementsByTagName('div');
//            foreach($divs as $div) {
//                // Loop through the DIVs looking for one withan id of "content"
//                // Then echo out its contents (pardon the pun)
//                if ($div->getAttribute('class') === 'content textbox smallgaps') {
//                    $courierHtml = $div->nodeValue;
//                }
//            }
            return $this->render('courier_tracker/view.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'loginUrl' => $this->loginUrl,
                'voucher' => $voucher
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}