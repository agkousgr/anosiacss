<?php

namespace App\Controller;

use App\Entity\{Address, User, WebUser};
use App\Service\CartService;
use App\Service\UserAccountService;
use App\Form\Type\{
    UserAddressType, UserGeneralInfoType, UserNewAddressType, UserRegistrationType
};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\TokenGenerator;
use Symfony\Component\Form\Extension\Core\Type\{PasswordType, RepeatedType};

class UserAccountController extends MainController
{
    /**
     * @var int Duration in seconds a password request will be active.
     */
    private static $timeToLive = 43200; // 12 hours

    public function userAccount(Request $request, UserAccountService $userAccountService)
    {
        try {
            if (null !== $this->loggedUser) {
                $user = new WebUser();
                $address = new Address();
                $userData = $userAccountService->getUserInfo($this->loggedUser, $user, $address);
                $userOrders = $userAccountService->getOrders($this->loggedClientId);
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
//                    dump($user);
                    $userAccountService->updateUserInfo($user);
                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user, $address);
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
                    $userAccountService->updateUserInfo($user);
                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user, $address);
                    $this->addFlash(
                        'success',
                        'Τα στοιχεία της διεύθυνσής σας ενημερώθηκαν με επιτυχία.'
                    );
                }

                return $this->render('user/account.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'userData' => $userData,
                    'formUser' => $formUser->createView(),
                    'formAddress' => $formMainAddress->createView(),
                    'loginUrl' => $this->loginUrl,
                    'userOrders' => $userOrders
                ]);
            }
            return $this->redirectToRoute('index');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewOrder(int $id, UserAccountService $userAccountService)
    {
        $userOrder = $userAccountService->getOrder($this->loggedClientId, $id);
        $voucherDisc = $userAccountService->getCoupon($userOrder['voucherId']);

        return $this->render('user/view_order.html.twig', [
            'categories' => $this->categories,
            'topSellers' => $this->topSellers,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedName' => $this->loggedName,
            'loggedUser' => $this->loggedUser,
            'order' => $userOrder,
            'voucherDisc' => $voucherDisc
        ]);
    }

    public function createAddress(Request $request, UserAccountService $userAccountService)
    {
        try {
            if (null !== $this->loggedUser) {
                $address = new Address();
                $formAddress = $this->createForm(UserNewAddressType::class, $address);
                $formAddress->handleRequest($request);
                if ($formAddress->isSubmitted() && $formAddress->isValid()) {
                    $address->setClient($this->loggedClientId);
                    $address->setAddress($formAddress->get('address')->getData());
                    $address->setZip($formAddress->get('zip')->getData());
                    $address->setCity($formAddress->get('city')->getData());
                    $address->setDistrict($formAddress->get('district')->getData());
                    $address->setName($formAddress->get('name')->getData());
                    if ($userAccountService->setAddress($address)) {
                        $this->addFlash(
                            'success',
                            'Η νέα σας διεύθυνση δημιουργήθηκε με επιτυχία.'
                        );
                        return $this->redirectToRoute('user_account');
                    } else {
                        $this->addFlash(
                            'notice',
                            'Ένα σφάλμα παρουσιάστηκε. Παρακαλώ δοκιμάστε ξανά. Αν το πρόβλημα συνεχίσει παρακαλούμε επικοινωνήστε μαζί μας.'
                        );
                    }
//                    $address = $userAccountService->updateUserInfo($user);
//                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user);
                }
                return $this->render('user/createAddress.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
//                'userData' => $userData,
//                'formUser' => $formUser->createView(),
                    'addressId' => '',
                    'formAddress' => $formAddress->createView(),
                    'loginUrl' => $this->loginUrl
                ]);
            }
            return $this->redirectToRoute('index');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function updateAddress(int $id, Request $request, EntityManagerInterface $em, UserAccountService $userAccountService)
    {
        try {
            if (null !== $this->loggedUser) {
                $address = new Address();
                $userAccountService->getAddress($this->loggedClientId, $address, $id);

                $formAddress = $this->createForm(UserNewAddressType::class, $address);
                $formAddress->handleRequest($request);
                if ($formAddress->isSubmitted() && $formAddress->isValid()) {
                    $address->setAddress($formAddress->get('address')->getData());
                    $address->setZip($formAddress->get('zip')->getData());
                    $address->setCity($formAddress->get('city')->getData());
                    $address->setDistrict($formAddress->get('district')->getData());
                    $address->setName($formAddress->get('name')->getData());
                    if ($userAccountService->setAddress($address)) {
                        $this->addFlash(
                            'success',
                            'Η διεύθυνσή σας ενημερώθηκε με επιτυχία.'
                        );
                        return $this->redirectToRoute('user_account');
                    } else {
                        $this->addFlash(
                            'notice',
                            'Ένα σφάλμα παρουσιάστηκε. Παρακαλώ δοκιμάστε ξανά. Αν το πρόβλημα συνεχίσει παρακαλούμε επικοινωνήστε μαζί μας.'
                        );
                    }
//                    $address = $userAccountService->updateUserInfo($user);
//                    $userData = $userAccountService->getUserInfo($this->loggedUser, $user);
                }
                return $this->render('user/createAddress.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
//                'userData' => $userData,
//                'formUser' => $formUser->createView(),
                    'addressId' => $address->getId(),
                    'formAddress' => $formAddress->createView(),
                    'loginUrl' => $this->loginUrl
                ]);
            }
            return $this->redirectToRoute('index');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function deleteAddress(Request $request, UserAccountService $user)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $id = $request->request->getInt('id');

                if (!$id) {
                    throw $this->createNotFoundException(
                        'No product found for id ' . $id
                    );
                }
                $response = $user->deleteAddress($id);
                if ($response === true) {
                    $this->addFlash(
                        'success',
                        'Η διεύθυνσή σας διαγράφηκε με επιτυχία.'
                    );
                } else {
                    $this->addFlash(
                        'notice',
                        'Ένα σφάλμα παρουσιάστηκε. Παρακαλώ δοκιμάστε ξανά. Αν το πρόβλημα συνεχίσει παρακαλούμε επικοινωνήστε μαζί μας.'
                    );
                }
                return $this->json(['success' => true]);
            } catch (\Exception $e) {
                $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
                throw $e;
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function register(Request $request, UserAccountService $userAccountService, UserPasswordEncoderInterface $encoder)
    {
        try {
            $user = new WebUser();
            $registerOk = 'false';
            $form = $this->createForm(UserRegistrationType::class);
            $form->handleRequest($request);
            $submittedToken = $request->request->get('_csrf_token');
            if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid('register', $submittedToken)) {

                $username = $form->get('username')->getData();
                $userAccountService->getUser($username, $user);
//                    dump($username, (string)$user["username"]);
                if ($username === $user->getUsername()) {
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
                            'Η εγγραφή σας ολοκληρώθηκε. Μπορείτε να συνδεθείτε για να συνεχίσετε τις αγορές σας.'
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
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedName' => $this->loggedName,
                'loggedUser' => $this->loggedUser,
                'form' => $form->createView(),
                'registerOk' => $registerOk,
                'loginUrl' => $this->loginUrl
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function forgotPassword()
    {
        return $this->render('user/forgot_password.html.twig', [
            'categories' => $this->categories,
            'topSellers' => $this->topSellers,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedName' => $this->loggedName,
            'loggedUser' => $this->loggedUser,
            'loginUrl' => $this->loginUrl
        ]);
    }

    public function sendPasswordEmail(
        Request $request, UserAccountService $accountService, TokenGenerator $tokenGenerator, \Swift_Mailer $mailer, LoggerInterface $logger
    )
    {
        if (!$this->isCsrfTokenValid('forgot_password', $request->request->get('_csrf_token')))
            return $this->render('user/forgot_password.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedName' => $this->loggedName,
                'loggedUser' => $this->loggedUser,
                'loginUrl' => $this->loginUrl,
                'error_message' => 'Η διάρκεια της φόρμας έληξε. Προσπαθήστε ξανά.',
            ]);

        try {
            $email = $request->request->get('email');
            $user = $accountService->getUser($email);

            if (false === $user || !isset($user->ID)) {
                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'error_message' => 'Ο χρήστης δε βρέθηκε. Προαπαθήστε ξανά.',
                ]);
            }
            if (intval($user->PasswordRequestCounter) >= 10) {
                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'error_message' => 'Μεγάλος αριθμός αιτήσεων από τον ίδιο χρήστη. Επικοινωνήστε με το διαχειριστή του συστήματος.',
                ]);
            }

            $userData = [];
            $dt = new \DateTime('now', new \DateTimeZone('Europe/Athens'));
            $confirmationToken = $tokenGenerator->generateToken();
            $userData['ID'] = $user->ID;
            $userData['ConfirmationToken'] = $confirmationToken;
            $userData['PasswordRequestDT'] = $dt->format('Y-m-d') . 'T' . $dt->format('H:i:s');
            $userData['ClientID'] = $user->ClientID;
            $userData['LastLoginDT'] = $user->LastLoginDT;
            $userData['Username'] = $user->Username;
            $userData['Password'] = $user->Password;
            $userData['IsActive'] = $user->IsActive;
            if (!intval($user->PasswordRequestCounter))
                $userData['PasswordRequestCounter'] = 1;
            else
                $userData['PasswordRequestCounter'] = intval($user->PasswordRequestCounter) + 1;

            $response = $accountService->updateUserAfterForgotPassword($userData);
            if ($response) {
                $message = (new \Swift_Message())
                    ->setSubject('Ανάκτηση Κωδικού')
                    ->setFrom('info@anosiapharmacy.gr')
                    ->setTo($email)
                    ->setBody($this->renderView('email/reset_password.txt.twig', [
                        'token' => $confirmationToken,
                    ]));
                $mailer->send($message);

                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'success_message' => 'Σας έχει αποσταλεί ένα e-mail με οδηγίες για την ανάκτηση του κωδικού σας. Ελέγξετε τα εισερχόμενά σας.',
                ]);
            } else {
                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'error_message' => 'Υπήρξε κάποιο πρόβλημα στη διαδικασία ανανέωσης του κωδικού. Παρακαλούμε προσπαθήστε ξανά.',
                ]);
            }

        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function resetPassword(Request $request, UserAccountService $accountService, LoggerInterface $logger)
    {
        try {
            $token = $request->query->getAlnum('token');
            $user = $accountService->getUserByToken($token);

            if (null === $token || false === $user) {
                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'error_message' => 'Ο χρήστης δε βρέθηκε. Προαπαθήστε ξανά.',
                ]);
            }

            $dt = new \DateTime($user->PasswordRequestDT, new \DateTimeZone('Europe/Athens'));
            if ($dt->getTimestamp() + self::$timeToLive < time()) {
                return $this->render('user/forgot_password.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'loggedName' => $this->loggedName,
                    'loggedUser' => $this->loggedUser,
                    'loginUrl' => $this->loginUrl,
                    'error_message' => 'Το προηγούμενο αίτημά σας έληξε. Προαπαθήστε ξανά.',
                ]);
            }

            $defaultData = [];
            $form = $this->createFormBuilder($defaultData)
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Επανάληψη Password'],
                    'invalid_message' => 'Τα passwords δεν είναι ίδια!',
                ])
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newPlainPass = $form->getData()['password'];
                $userData = [];
                $userData['ID'] = $user->ID;
                $userData['ClientID'] = $user->ClientID;
                $userData['LastLoginDT'] = $user->LastLoginDT;
                $userData['Username'] = $user->Username;
                $userData['IsActive'] = $user->IsActive;
                $userData['PasswordRequestCounter'] = $user->PasswordRequestCounter;
                $userData['Password'] = password_hash($newPlainPass, PASSWORD_DEFAULT);

                $accountService->updateUserAfterForgotPassword($userData, true);
                $this->addFlash('success', 'Το password άλλαξε με επιτυχία!');

                return $this->redirectToRoute('index');
            }

            return $this->render('user/reset_password.html.twig', [
                'form' => $form->createView(),
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedName' => $this->loggedName,
                'loggedUser' => $this->loggedUser,
                'loginUrl' => $this->loginUrl,
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function logout(EntityManagerInterface $em, CartService $cartService)
    {
        $cartService->clearCart($em, $this->session->remove('anosiaUser'));
        $this->session->remove('anosiaUser');
        $this->session->remove('anosiaName');
        $this->session->remove('curOrder');
        $this->session->remove('couponDisc');
        $this->session->remove('couponName');
        $this->session->remove('couponId');
        $this->session->remove("addAddress");
        return $this->redirectToRoute('index');
    }

}
