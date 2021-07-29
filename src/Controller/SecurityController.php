<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login", priority=1)
     */
    public function login(Security $security, Request $request, RequestStack $requestStack): Response
    {
        $user = $security->getUser();
        if($user){
            return $this->redirectToRoute("security_logout");
        }
        $messageError = null;
        $session = $requestStack->getSession();
        $mail = isset($request->get("login")["email"])?$request->get("login")["email"]:"";
        
        if($session->get("messageError")){
            $messageError = $session->get("messageError");
            $session->remove("messageError");
        }


        $form = $this->createForm(LoginType::class, ["email"=>$mail]);

        return $this->render('security/login.html.twig', [
            "formView"=>$form->createView(),
            "error"=>$messageError
        ]);
    }


    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){
        return $this->redirectToRoute("security_login");
    }
}
