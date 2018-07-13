<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 9/7/2018
 * Time: 7:57 μμ
 */

namespace App\Controller;

use App\Security\User\WebserviceUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserAccountService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AjaxLoginController extends AbstractController
{
    public function login(Request $request, UserAccountService $userAccountService, WebserviceUserProvider $provider, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                dump($request);
                $username = $request->request->get('username');
                $password = $request->request->get('password');
//                if ($userAccountService->login($username, $password)) {
//                if ($provider->loadUserByUsername($username)) {
                    if ($userAccountService->login($username, $password) === $username) {
                        $session->set("anosiaUser");
//                        $provider->loadUserByUsername($username);
                        return $this->json([
                            'success' => true,
                        ]);
//                    } else {
//                        return $this->json([
//                            'success' => false,
//                            'errorMsg' => 'Ο κωδικός χρήστη είναι λανθασμένος. Παρακαλούμε δοκιμάστε ξανά'
//                        ]);
//                    }
                } else {
                    return $this->json([
                        'success' => false,
                        'errorMsg' => 'Το όνομα χρήστη ή ο κωδικός που εισάγατε είναι λανθασμένος. Παρακαλώ προσπαθήστε ξανά!'
                    ]);
                }
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }

    }
}