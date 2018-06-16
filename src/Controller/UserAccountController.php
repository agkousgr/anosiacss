<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2/6/2018
 * Time: 9:09 μμ
 */

namespace App\Controller;


use App\Form\Type\UserRegistrationType;
use App\Service\{
    CategoryService, UserAccountService, SoftoneLogin
};
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, PasswordType
};
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserAccountController extends AbstractController
{
    public function index(Request $request, SoftoneLogin $softoneLogin, CategoryService $categoryService, LoggerInterface $logger, SessionInterface $session, UserAccountService $userAccountService)
    {
        try {
            $softoneLogin->login();
            $categories = $categoryService->getCategories();
            array_multisort(array_column($categories, "priority"), $categories);
//            $defaultData = array('message' => 'Type your message here');
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
                $userExists = $userAccountService->userAlreadyExists($data["username"], $session->get("authID"));
                dump($data["username"]);
                if (null === $userExists) {
                    return $this->redirectToRoute('user_registration');
                    $form = $this->createForm(UserRegistrationType::class, [
                        'username' => $data["username"]
                    ]);
                    $form->handleRequest($request);
                    return $this->render('user_account/register.html.twig', [
                        'categories' => $categories,
                        'username' => $data["username"],
                        'form' => $form->createView()
                    ]);
                }
            }
            if ($formSignIn->isSubmitted() && $formSignIn->isValid()) {
                $data = $formSignIn->getData();
            }

            return $this->render('user_account/index.html.twig', [
                'categories' => $categories,
                'formSignUp' => $formSignUp->createView(),
                'formSignIn' => $formSignIn->createView(),
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function register(Request $request, SoftoneLogin $softoneLogin, CategoryService $categoryService, LoggerInterface $logger, SessionInterface $session, UserAccountService $userAccountService)
    {
        try {
            $softoneLogin->login();
            $categories = $categoryService->getCategories();
            array_multisort(array_column($categories, "priority"), $categories);

            $form = $this->createForm(UserRegistrationType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $userAccountService->createUser($form, $session->get("authID"));
            }

            return $this->render('user_account/register.html.twig', [
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}