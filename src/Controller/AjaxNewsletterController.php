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
                $gender = $request->request->get('gender');
                $age = $request->request->get('age');
                if (!$name OR !$email) {
                    return $this->json([
                        'success' => 'empty',
                    ]);
                } else {
                    $date = date('Y-m-d H:i:s');
                    $referrer = 'USER AGENT: ' . $request->headers->get('User-Agent') . ' REFERRER: ' . $request->headers->get('referer') . ' DATE: ' . $date;
                    $userIsRegistered = $newsletterService->getNewsletter($name, $email, $referrer);
                    dump($userIsRegistered);
                    return $this->json([
                        'success' => $userIsRegistered["success"],
                        'exist' => $userIsRegistered["exist"]
                    ]);

//                    switch ($userIsRegistered) {
//                        case 'UserExists':
//                            return $this->json([
//                                'success' => true,
//                                'exist' => true
//                            ]);
//                            break;
//
//                        case 'Success':
//                            return $this->json([
//                                'success' => true,
//                                'exist' => false
//                            ]);
//                            break;
//                    }
                }

            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}