<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
{

    protected $session;

    public function __construct(RequestStack $requestStack)
    {
       $this->session = $requestStack->getSession();
    }




    public function supports(Request $request): ?bool
    {
        //Toujours avoirs l'url sous la main même pour les méthodes n'ayant pas accés à Request
        $this->session->set("urlOrigine", $request->getPathInfo());
        //Définition les conditions du contrôle de login
        $authCondition = ($request->attributes->get("_route") === "security_login" && $request->isMethod("POST"));
        //S'il ne s'agît pas d'une tentative de connexion via /login
        if($this->session->get("tryToConnectRoute") && $request->getPathInfo()!=="/login"){
            $this->session->remove("tryToConnectRoute");
        }
        return $authCondition;
    }


    public function authenticate(Request $request): PassportInterface
    {
        $credentials = $request->request->get('login');
        return new Passport(
            new UserBadge($credentials['email']), 
            new PasswordCredentials($credentials['password'])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $redirectUrl = $this->session->get("tryToConnectRoute")?$this->session->get("tryToConnectRoute"):"/";
        $this->session->remove("tryToConnectRoute");
        $this->session->remove("messageError");
        return new RedirectResponse($redirectUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $messageError = $exception->getMessage();
        if($messageError=="The presented password is invalid."){
            $messageError = "Le mot de passe n'est pas valide";
        }
        elseif($messageError=="Bad credentials."){
            $messageError = "Adresse e-mail inconnue";
        }
        $this->session->set("messageError", $messageError);
        return null;
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntrypointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
