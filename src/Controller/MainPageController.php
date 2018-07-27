<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Login;
use App\Form\RegisterUser;
use App\Utils\UserService;
use DateTime;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class MainPageController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function mainPage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $register = new RegisterUser();
        $login = new Login();
        $userService = new UserService($em);
        $session = new Session();
        $session->clear();

        $loginForm = $this->get('form.factory')
            ->createNamedBuilder('login_form', FormType::class, $login)
            ->add('login', TextType::class)
            ->add('password', PasswordType::class)
            ->add('logIn', SubmitType::class, array('label' => 'Log In'))
            ->getForm();
        $loginForm->handleRequest($request);


        $registerForm = $this->get('form.factory')
            ->createNamedBuilder('register_form', FormType::class, $register)
            ->add('login', TextType::class)
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('passwordRepeated', PasswordType::class)
            ->add('register', SubmitType::class, array('label' => 'Register!'))
            ->getForm();
        $registerForm->handleRequest($request);


        if (($registerForm->isSubmitted() && $registerForm->isValid()) || ($loginForm->isSubmitted() && $loginForm->isValid())) {

            if ($request->request->get('register_form')) {
                $register = $registerForm->getData();

                $isRegisteredSuccessfully = $userService->registerUser($register);

                if($isRegisteredSuccessfully === true){
                    return $this->render('base.html.twig', array(
                        'registerForm' => $registerForm->createView(),
                        'loginForm' => $loginForm->createView(),
                        'message'=>"Account successfully created - call admin to activate it",
                    ));
                }else{
                    return $this->render('base.html.twig', array(
                        'registerForm' => $registerForm->createView(),
                        'loginForm' => $loginForm->createView(),
                        'message'=>$isRegisteredSuccessfully,
                    ));
                }
            }else if ($request->request->get('login_form')) {
                $loggedUser = $userService->loginUser($login);

                if($loggedUser === 0){
                    return $this->render('base.html.twig', array(
                        'registerForm' => $registerForm->createView(),
                        'loginForm' => $loginForm->createView(),
                        'message'=>"Account not activated",
                    ));
                }else if ($loggedUser !== false){
                    $session->set('loggedUser', $login->getLogin());
                    $session->set('userRoles', unserialize($loggedUser['0']['roles']));

                    return $this->redirectToRoute('user_page');
                }else{
                    return $this->render('base.html.twig', array(
                        'registerForm' => $registerForm->createView(),
                        'loginForm' => $loginForm->createView(),
                        'message'=>"Wrong login or password",
                    ));
                }
            }
        }

        return $this->render('base.html.twig', array(
            'registerForm' => $registerForm->createView(),
            'loginForm' => $loginForm->createView(),
        ));
    }

    /**
     * @Route("/user", name="user_page")
     */
    public function users()
    {
        $session = new Session();
        $loggedUser = $session->get("loggedUser");

        if(!$loggedUser)
            return $this->redirectToRoute("main_page");

        $userRoles = $session->get("userRoles");

        return $this->render('user/index.html.twig', array("user"=>$loggedUser, "roles"=>$userRoles));
    }
}
