<?php

namespace App\AccessManager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AccessBlocker extends AbstractController{

    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Crée un bloqueur automatique d'accès avec redirection sur la page de login si l'utilisateur n'est pas connecté
     * Avec redirection automatique vers une url d'origine et affichage des erreurs
     *
     * @param bool $condition Définit la condition pour accéder à la ressource
     * @param string $messageNoAccess Message si l'utilisateur n'a pas le bon rôle
     * @param string $messageNoUser Message si l'utilisateur n'est pas connecté
     * @return void
     */
    public function block_access(
    bool $condition=false,
    string $messageNoAccess="Vous ne disposez pas des droits suffisants",
    string $messageNoUser="Vous devez être connecté pour accéder à cette page"){
        
        $session = $this->requestStack->getSession();
        $user = $this->getUser();
        
        if(!$user){
            $session->set("messageError", $messageNoUser);
            $session->set("tryToConnectRoute", $session->get("urlOrigine"));
            return $this->redirectToRoute("security_login");
        }

        elseif(!$condition){
            $session->set("messageError", $messageNoAccess);
            return $this->redirectToRoute("homepage");
        }

        
    }
}