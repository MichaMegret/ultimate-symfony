<?php 

namespace App\EventDispatcher;

use App\Event\ProductShowEvent;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductShowEmailSuscriber implements EventSubscriberInterface{

    protected $session;

    public function __construct(SessionInterface $session)
    {
     $this->session = $session;   
    }

    public static function getSubscribedEvents()
    {
        return [
            "product.show"=>"sendAdminEmail"
        ];
    }

    public function sendAdminEmail(ProductShowEvent $productShowEvent){
        //dd($purchaseSuccessEvent->getPurchase());
        $this->session->set("Message transfert", "Message envoyé à l'administrateur");
        $this->session->set("Product event", $productShowEvent);

    }
}