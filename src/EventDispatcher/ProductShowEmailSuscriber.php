<?php 

namespace App\EventDispatcher;

use App\Event\ProductShowEvent;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductShowEmailSuscriber implements EventSubscriberInterface{

    protected $session;
    protected $mailer;

    public function __construct(SessionInterface $session, MailerInterface $mailer)
    {
     $this->session = $session;   
     $this->mailer = $mailer;
    }

    // Activer/Désactiver le branchement à l'événement
    public static function getSubscribedEvents()
    {
        return [
            //"product.show"=>"sendAdminEmail"
        ];
    }

    public function sendAdminEmail(ProductShowEvent $productShowEvent){
        //dd($purchaseSuccessEvent->getPurchase());

        // Création d'un email basique
        //-----------------------------------------------------------------------------------------------------------------
        // $email = new Email();
        // $email->from(new Address("contact@mail.com", "Info de la boutique"))
        //     ->to("admin1@mail.com")
        //     ->text("Un visiyeur est en train de voir la page du produit numéro ".$productShowEvent->getProduct()->getId())
        //     ->html("<h1>Visite du produit n°{$productShowEvent->getProduct()->getId()}</h1>")
        //     ->subject("Visite du produit n° ".$productShowEvent->getProduct()->getId());

        $email = new TemplatedEmail();
        $email
            ->from(new Address("contact@mail.com", "Info de la boutique"))
            ->to("admin1@mail.com")
            ->subject("Visite du produit n° ".$productShowEvent->getProduct()->getId())
            ->text("Un visiyeur est en train de voir la page du produit numéro ".$productShowEvent->getProduct()->getId())
            ->htmlTemplate("email/product_show.html.twig")
            ->context([
                "product"=>$productShowEvent->getProduct()
            ]);

        $this->mailer->send($email);
    }
}