<?php 

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PurchaseSuccessEmailSuscriber implements EventSubscriberInterface{

    protected $session;

    public function __construct(SessionInterface $session)
    {
     $this->session = $session;   
    }

    public static function getSubscribedEvents()
    {
        return [
            "purchase.success"=>"sendSuccessEmail"
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent){
        //dd($purchaseSuccessEvent->getPurchase());
        $this->session->set("Message transfert", "Message modifié après validation du paiement");
    }
}