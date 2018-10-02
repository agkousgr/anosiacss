<?php

namespace App\Controller;

use App\Security\User\WebserviceUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\UserAccountService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use \Facebook;


class AjaxLoginController extends AbstractController
{
    public function login(Request $request, UserAccountService $userAccountService, WebserviceUserProvider $provider, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $username = $request->request->get('username');
                $password = $request->request->get('password');
//                if ($userAccountService->login($username, $password)) {
//                if ($provider->loadUserByUsername($username)) {
                if ($userAccountService->login($username, $password) === $username) {
                    $session->set("anosiaUser", $username);
                    $session->remove('curOrder');
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

    public function fbcallback(Request $request, UserAccountService $userAccountService, SessionInterface $session)
    {
        $fb = new Facebook\Facebook([
            'app_id' => '605092459847380',
            'app_secret' => '09f4a59ad57726736664a92d7059025f',
            'default_graph_version' => 'v3.0',
        ]);
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        echo '<h3>Access Token</h3>';
        var_dump($accessToken->getValue());

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        echo '<h3>Metadata</h3>';
        var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId('605092459847380');
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            echo '<h3>Long-lived</h3>';
            var_dump($accessToken->getValue());
        }

//        $_SESSION['fb_access_token'] = (string) $accessToken;
        $res = $fb->get('/me?fields=name,email', $accessToken);

        print_r($res->getDecodedBody());
        // Todo: If button call is via ajax change return code
        if ($userAccountService->login($res->getDecodedBody()['email'], $res->getDecodedBody()['id'])) {
            $session->set("anosiaUser", $res->getDecodedBody()['email']);
            $session->remove('curOrder');
        } else {
            $user = [];
            $user['username'] = $res->getDecodedBody()['email'];
            list($firstname, $lastname) = explode(' ', $res->getDecodedBody()['name']);
            $lastname = ($lastname) ?: ' ';
            $user['firstname'] = $firstname;
            $user['lastname'] = $lastname;
            $user['password'] = $res->getDecodedBody()['id'];

            if ('Success' !== $createUserResult = $userAccountService->createUser($user)) {
                $userAccountService->login($res->getDecodedBody()['id'], $res->getDecodedBody()['id']);
                $session->set("anosiaUser", $res->getDecodedBody()['id']);
                $session->remove('curOrder');
            }
        }
        return $this->redirectToRoute('index');

        // User is logged in with a long-lived access token.
        // You can redirect them to a members-only page.
        //

    }
}