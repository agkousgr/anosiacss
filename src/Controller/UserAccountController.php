<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2/6/2018
 * Time: 9:09 μμ
 */

namespace App\Controller;

use App\Entity\{Address, User, WebUser};
use App\Service\CartService;
use App\Service\UserAccountService;
use App\Form\Type\{
    UserAddressType, UserGeneralInfoType, UserInfoType, UserNewAddressType, UserRegistrationType
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAccountController extends MainController
{
    public function userAccount(Request $request, UserAccountService $userAccountService)
    {
        try {
            dump($request);
            if (null !== $this->loggedUser) {
                $user = new WebUser();
                $address = new Address();
                $userData = $userAccountService->getUserInfo($this->loggedUser, $user, $address);
                $userOrders = $userAccountService->getOrders($this->loggedClientId);
                dump($userOrders);
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
                dump($userData);
                return $this->render('user/account.html.twig', [
                    'categories' => $this->categories,
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
//        dump($userOrders);
        return $this->render('user/view_order.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedName' => $this->loggedName,
            'loggedUser' => $this->loggedUser,
            'order' => $userOrder,
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

    public function logout(EntityManagerInterface $em, CartService $cartService)
    {
        $cartService->clearCart($em, $this->session->remove('anosiaUser'));
        $this->session->remove('anosiaUser');
        $this->session->remove('anosiaName');
        $this->session->remove('curOrder');
        $this->session->remove('couponDisc');
        $this->session->remove('couponName');
        $this->session->remove('couponId');
        return $this->redirectToRoute('index');
    }

}
