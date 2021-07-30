<?php 

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

// Renomer la classe et le fichier en PrenomListener, utiliser la configuration dans service.yaml 
// et ne pas implémenter EventSubscriberInterface ni getSubscribedEvents
// pour revenir en mode écouteur d'événements au lieu de Suscriber (==>Equivalent mais plus long)
class PrenomSuscriber implements EventSubscriberInterface{
    
    public static function getSubscribedEvents()
    {
        return [
            "kernel.request"=>"addPrenomToAttributes",
            // "kernel.controller"=>"test1",
            // "kernel.response"=>"test2"
        ];
    }

    public function addPrenomToAttributes(RequestEvent $requestEvent){
        $requestEvent->getRequest()->attributes->set("prenom", "Micha");
    }

    public function test1(){
        dump("test 1");
    }

    public function test2(){
        dump("test 2");
    }
}