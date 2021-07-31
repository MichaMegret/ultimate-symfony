<?php 

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSuscriber implements EventSubscriberInterface{

    protected $session;
    protected $mailer;
    protected $security;

    public function __construct(SessionInterface $session, MailerInterface $mailer, Security $security)
    {
     $this->session = $session;   
     $this->mailer = $mailer;
     $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            "purchase.success"=>"sendSuccessEmail"
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent){
        $purchase = $purchaseSuccessEvent->getPurchase();
        /** @var User */
        $purchaseUser = $purchase->getUser();
        $emailUser = $purchaseUser->getEmail();
        $nameUser = $purchaseUser->getFullName();

        $email = new TemplatedEmail();

        $email
            ->to(new Address($emailUser, $nameUser))
            ->from("contact_leshopdamande@mail.com")
            ->subject("Confirmation de la commande n°{$purchase->getId()}")
            ->htmlTemplate("email/purchase_success.html.twig")
            ->context([
                "purchase"=>$purchase,
                "user"=>$purchaseUser
            ]);

        $this->mailer->send($email);
    }
}