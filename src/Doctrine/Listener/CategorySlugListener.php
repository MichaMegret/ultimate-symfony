<?php 

namespace App\Doctrine\Listener;

use App\Entity\Category;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class CategorySlugListener{

    protected $slugify;

    public function __construct(Slugify $slugify){
        $this->slugify = $slugify;
    }

    public function prePersist(Category $entity,LifecycleEventArgs $event){

        if(empty($entity->getSlug())){
            $entity->setSlug(strtolower($this->slugify->slugify($entity->getName())));
        }

    }
}