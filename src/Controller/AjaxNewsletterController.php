<?php


namespace App\Controller;


use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AjaxNewsletterController extends AbstractController
{
    public function newsletterRegistration(Request $request, NewsletterService $newsletterService)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $name = $request->request->get('name');
                $email = $request->request->get('email');
                if (!$name OR !$email) {
                    return $this->json([
                        'success' => 'empty',
                    ]);
                }else {
                    $userIsRegistered = $newsletterService->getNewsletter($name, $email);

                }
                return $this->json([
                    'success' => true,
                    'exist' => false
                ]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
}
}