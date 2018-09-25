<?php

namespace App\Controller;


use App\Service\ProductService;
use App\Service\SkroutzService;
use Monolog\Logger;

class SkroutzController extends MainController
{
    function createXML(SkroutzService $skroutzService)
    {
        try {
            $xml = $skroutzService->initilizeSkroutzXml();
            dump($xml);
            return $this->render('skroutz/xml.html.twig', [
                'xml' => $xml
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}