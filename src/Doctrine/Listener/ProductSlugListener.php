<?php 

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * Ajoute automatique du sug d'un produit à partir de son nom lors de sa création
 */
class ProductSlugListener{

    protected $slugify;

    public function __construct(Slugify $slugify){
        $this->slugify = $slugify;
    }

    // Définition d'une méthode prePersist automatiquement appellée par Doctrine lors de l'événement PrePersist si defini dans service.yaml
    // Les branchements sont faits dans service.yaml
    // Le problème est que cette fonction est appellée à avant chaque persist, même s'il ne concerne pas une création de produit
    // Pour changer cela, modification de service.yaml, qui nous renverra à partir de là une entité Product
    public function prePersist(Product $entity,LifecycleEventArgs $event){

        // Ce qui suit n'est nécessaire que si le name tag dans service.yaml est doctrine.event_listener 
        // et que on ne recoit pas d'entité Product
        // $entity = $event->getObject();

        // if(!($entity instanceof Product)){
        //     return;
        // }

        if(empty($entity->getSlug())){
            $entity->setSlug(strtolower($this->slugify->slugify($entity->getName())));
        }

    }
}