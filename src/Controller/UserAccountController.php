<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2/6/2018
 * Time: 9:09 μμ
 */

namespace App\Controller;

use App\Service\UserAccountService;
use App\Form\Type\UserRegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, PasswordType
};

class UserAccountController extends MainController
{
    public function index(Request $request, UserAccountService $userAccountService)
    {
        try {
            $formSignUp = $this->createFormBuilder()
                ->add('username', EmailType::class)
                ->getForm();

            $formSignIn = $this->createFormBuilder()
                ->add('username', EmailType::class)
                ->add('password', PasswordType::class)
                ->getForm();

            $formSignUp->handleRequest($request);
            $formSignIn->handleRequest($request);
            if ($formSignUp->isSubmitted() && $formSignUp->isValid()) {
                $data = $formSignUp->getData();
                $userExists = $userAccountService->userAlreadyExists($data["username"], $this->session->get("authID"));
                // add flash message
                dump($data["username"]);
                if (null === $userExists) {
                    return $this->redirectToRoute('user_registration');
                    $form = $this->createForm(UserRegistrationType::class, [
                        'username' => $data["username"]
                    ]);
                    $form->handleRequest($request);
                    return $this->render('user/register.html.twig', [
                        'categories' => $this->categories,
                        'popular' => $this->popular,
                        'featured' => $this->featured,
                        'cartItems' => $this->cartItems,
                        'totalCartItems' => $this->totalCartItems,
                        'loggedUser' => $this->loggedUser,
                        'form' => $form->createView()
                    ]);
                }
            }
            if ($formSignIn->isSubmitted() && $formSignIn->isValid()) {
                $data = $formSignIn->getData();
            }

            return $this->render('user/index.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'formSignUp' => $formSignUp->createView(),
                'formSignIn' => $formSignIn->createView(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function register(Request $request, UserAccountService $userAccountService)
    {
        try {
            $form = $this->createForm(UserRegistrationType::class);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $newUser = $userAccountService->createUser($form->getData());
                dump($newUser);
            }

            return $this->render('user/register.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'form' => $form->createView(),
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function login(Request $request, UserAccountService $userAccountService)
    {
        dump($request);
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $userLoggedIn = $userAccountService->login($username, $password);
        if ($userLoggedIn) {
            $this->addFlash(
                'notice',
                'Συνδεθήκατε με επιτυχία'
            );
        } else {
            $this->addFlash(
                'notice',
                'Παρουσιάστηκε σφάλμα κατά την είσοδο. Παρακαλώ προσπαθήστε ξανά!'
            );
        }

        return ($this->render('orders/cart.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'loggedUser' => $this->loggedUser,
        ]));

    }

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