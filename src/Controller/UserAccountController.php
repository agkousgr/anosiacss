<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2/6/2018
 * Time: 9:09 μμ
 */

namespace App\Controller;

use App\Entity\Address;
use App\Entity\WebUser;
use App\Security\User\WebserviceUser;
use App\Service\UserAccountService;
use App\Form\Type\{
    UserAddressType, UserGeneralInfoType, UserInfoType, UserRegistrationType
};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, PasswordType
};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAccountController extends MainController
{
//    public function index(Request $request, UserAccountService $userAccountService)
//    {
//        try {
//            $formSignUp = $this->createFormBuilder()
//                ->add('username', EmailType::class)
//                ->getForm();
//
//            $formSignIn = $this->createFormBuilder()
//                ->add('username', EmailType::class)
//                ->add('password', PasswordType::class)
//                ->getForm();
//
//            $formSignUp->handleRequest($request);
//            $formSignIn->handleRequest($request);
//            if ($formSignUp->isSubmitted() && $formSignUp->isValid()) {
//                $data = $formSignUp->getData();
//                $userExists = $userAccountService->userAlreadyExists($data["username"], $this->session->get("authID"));
//                // add flash message
//                dump($data["username"]);
//                if (null === $userExists) {
//                    return $this->redirectToRoute('user_registration');
//                    $form = $this->createForm(UserRegistrationType::class, [
//                        'username' => $data["username"]
//                    ]);
//                    $form->handleRequest($request);
//                    return $this->render('user/register.html.twig', [
//                        'categories' => $this->categories,
//                        'popular' => $this->popular,
//                        'featured' => $this->featured,
//                        'cartItems' => $this->cartItems,
//                        'totalCartItems' => $this->totalCartItems,
//                        'loggedUser' => $this->loggedUser,
//                        'form' => $form->createView()
//                    ]);
//                }
//            }
//            if ($formSignIn->isSubmitted() && $formSignIn->isValid()) {
//                $data = $formSignIn->getData();
//            }
//
//            return $this->render('user/index.html.twig', [
//                'categories' => $this->categories,
//                'popular' => $this->popular,
//                'featured' => $this->featured,
//                'cartItems' => $this->cartItems,
//                'totalCartItems' => $this->totalCartItems,
//                'loggedUser' => $this->loggedUser,
//                'formSignUp' => $formSignUp->createView(),
//                'formSignIn' => $formSignIn->createView(),
//            ]);
//        } catch (\Exception $e) {
//            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
//            throw $e;
//        }
//    }

    public function userAccount(Request $request, UserAccountService $userAccountService)
    {
        try {
            dump($request);
            if (null !== $this->loggedUser) {
                $user = new WebUser();
                $userData = $userAccountService->getUserInfo($this->loggedUser, $user);
                dump($user, $userData);
//            $user = new WebserviceUser(
//                $userData["clientId"],
//                $userData["username"],
//                $userData["password"],
//                $userData["name"],
//                $userData["name"],
//                $userData["newsletter"],
//                '',
//                []
//            );
                $formUser = $this->createForm(UserGeneralInfoType::class, $user);
                $formUser->handleRequest($request);
                $formMainAddress = $this->createForm(UserAddressType::class, $user);
                $formMainAddress->handleRequest($request);
                if ($formUser->isSubmitted() && $formUser->isValid()) {
                    $user->setFirstname($formUser->get('firstname')->getData());
                    $user->setLastname($formUser->get('lastname')->getData());
                    $user->setNewsletter($formUser->get('newsletter')->getData());
                    dump($user);
                    $userAccountService->updateUserInfo($user);
                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user);
                    $this->addFlash(
                        'success',
                        'Τα στοιχεία σας ενημερώθηκαν με επιτυχία.'
                    );
                }
                if ($formMainAddress->isSubmitted() && $formMainAddress->isValid()) {
                    $user->setAddress($formMainAddress->get('address')->getData());
                    $user->setZip($formMainAddress->get('zip')->getData());
                    $user->setCity($formMainAddress->get('city')->getData());
                    $user->setDistrict($formMainAddress->get('district')->getData());
                    $user->setPhone01($formMainAddress->get('phone01')->getData());
                    $address = $userAccountService->updateUserInfo($user);
                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user);
                    $this->addFlash(
                        'success',
                        'Τα στοιχεία της διεύθυνσής σας ενημερώθηκαν με επιτυχία.'
                    );
                }
                return $this->render('user/account.html.twig', [
                    'categories' => $this->categories,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'userData' => $userData,
                    'formUser' => $formUser->createView(),
                    'formAddress' => $formMainAddress->createView(),
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function createAddress(Request $request, UserAccountService $userAccountService)
    {
        try {

        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function register(Request $request, UserAccountService $userAccountService, UserPasswordEncoderInterface $encoder)
    {
        try {
//            $user = new WebserviceUser();
            $registerOk = 'false';
            $form = $this->createForm(UserRegistrationType::class);
            $form->handleRequest($request);
            $submittedToken = $request->request->get('_csrf_token');
            if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('register', $submittedToken)) {

                $username = $form->get('username')->getData();
                $user = $userAccountService->getUser($username);
//                    dump($username, (string)$user["username"]);
                if ($username === (string)$user["username"]) {
                    $this->addFlash(
                        'notice',
                        'Υπάρχει ήδη χρήστης με αυτό το email. Αν δεν θυμάστε τον κωδικό σας πατήστε στο "Ξεχάσατε τον κωδικό σας?".'
                    );
                } else {
//                    $password = $encoder->encodePassword($user, $user->getPlainPassword());
//                    $user->set
                    $newUser = $userAccountService->createUser($form->getData());
                    if ($newUser === 'Success') {
                        $this->addFlash(
                            'success',
                            'Η εγγραφή σας ολοκληρώθηκε. Μπορείτε να συνδεθείτε για να συνεχίσετε τα ψώνια σας.'
                        );
                        $registerOk = 'true';
                    } else {
                        $this->addFlash(
                            'notice',
                            'Παρουσιάστηκε σφάλμα κατά την εγγραφή. Παρακαλώ δοκιμάστε ξανά. Αν το πρόβλημα συνεχιστεί παρακαλώ επικοινωνήστε μαζί μας.'
                        );
                    }
                }
            }

            return $this->render('user/register.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedName' => $this->loggedName,
                'loggedUser' => $this->loggedUser,
                'form' => $form->createView(),
                'registerOk' => $registerOk
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function logout()
    {
        $this->session->remove('anosiaUser');
        $this->session->remove('anosiaName');
        return $this->redirectToRoute('index');
    }

//    public function login(Request $request, UserAccountService $userAccountService)
//    {
//        if ($request->isXmlHttpRequest()) {
//            try {
//                $username = $request->request->get('username');
//                $password = $request->request->get('password');
//
//                if ($userAccountService->login($username, 'null')) {
//                    if ($userAccountService->login($username, $password)) {
//                        return $this->json([
//                            'success' => true,
//                        ]);
//                    } else {
//                        return $this->json([
//                            'success' => false,
//                            'errorMsg' => 'Ο κωδικός χρήστη είναι λανθασμένος. Παρακαλούμε δοκιμάστε ξανά'
//                        ]);
//                    }
//                } else {
//                    return $this->json([
//                        'success' => false,
//                        'errorMsg' => 'Το όνομα χρήστη που εισάγατε δεν αντιστοιχεί σε χρήστη. Παρακαλώ προσπαθήστε ξανά!'
//                    ]);
//                }
//            } catch (\Exception $e) {
//                throw $e;
//            }
//        } else {
//            throw $this->createNotFoundException('The resource you are looking for could not be found.');
//        }
//
//    }

    public function getUsers(UserAccountService $userAccountService)
    {
        try {
            $users = $userAccountService->getUsers();
//            die();

            return $this->render('user/register.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'loggedUser' => $this->loggedUser,
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}