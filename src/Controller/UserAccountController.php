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
    CategoryService, ProductService, UserAccountService, SoftoneLogin
};
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, PasswordType
};
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
                dump($data["username"]);
                if (null === $userExists) {
                    return $this->redirectToRoute('user_registration');
                    $form = $this->createForm(UserRegistrationType::class, [
                        'username' => $data["username"]
                    ]);
                    $form->handleRequest($request);
                    return $this->render('user/register.html.twig', [
                        'categories' => $this->categories,
                        'featured' => $this->featured,
                        'username' => $data["username"],
                        'form' => $form->createView()
                    ]);
                }
            }
            if ($formSignIn->isSubmitted() && $formSignIn->isValid()) {
                $data = $formSignIn->getData();
            }

            return $this->render('user/index.html.twig', [
                'categories' => $this->categories,
                'featured' => $this->featured,
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
                $userAccountService->createUser($form, $this->session->get("authID"));
            }

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