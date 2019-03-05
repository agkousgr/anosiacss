<?php

namespace App\Controller;


use App\Service\SkroutzService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;

class SkroutzController extends AbstractController
{
    public function createXML(SkroutzService $skroutzService, LoggerInterface $logger)
    {
        $fileSystem = new Filesystem();
        try {
            $xml = $skroutzService->initilizeSkroutzXml();
            $fileName = '../public/uploads/skroutz.xml';
            if ($fileSystem->exists($fileName)) {
                $fileSystem->remove($fileName);
            }
            dump($xml);
            $fileSystem->appendToFile($fileName, $xml);
//            dump($xml);
//            $textResponse = new Response($this->render('skroutz/xml.html.twig', [
//                'xml' => htmlentities($xml)
//            ] ));

//            $textResponse->headers->set('Content-Type', 'xml');
            return $this->json(['success' => true, 'message' => 'XML File updated succesfully']);
//            return $this->render('skroutz/xml.html.twig', [
//                'xml' => $xml
//            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}