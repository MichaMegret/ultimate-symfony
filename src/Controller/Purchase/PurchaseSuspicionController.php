<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Entity\PurchaseSuspicion;
use App\Repository\PurchaseRepository;
use App\Repository\PurchaseSuspicionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseSuspicionController extends AbstractController{


    /**
     * @Route("/purchase/suspicion/unlock/{id}", name="purchase_unlock_suspicion")
     */
    public function unlockSuspicion($id, SessionInterface $session, PurchaseRepository $purchaseRepository){

        /** @var Purchase */
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getStatut() === Purchase::STATUT_PAID) 
        ) {
            return;
        }

        $session->set("unlock_purchase_suspicion", $id);

        return;
    }

    /**
     * @Route("/purchase/suspicion/{id}", name="purchase_suspicion")
     */
    public function createSuspicion($id, SessionInterface $session, PurchaseRepository $purchaseRepository, EntityManagerInterface $manager){
        
        /** @var Purchase */
        $purchase = $purchaseRepository->find($id);
      
        if (
            !$purchase ||
            ($purchase && $purchase->getStatut() === Purchase::STATUT_PAID) ||
            ($session->get("unlock_purchase_suspicion") !== $id)
        ) {
            return;
        }

        $suspicion = new PurchaseSuspicion;

        $suspicion->setPurchase($purchase);
        $suspicion->setDate(new DateTime());


        $manager->persist($suspicion);
        $manager->flush();

        $session->remove("unlock_purchase_suspicion");
        
        return;
    }



    

    


    /**
     * @Route("/purchase/unsuspect/{id<\d+>}", name="purchase_unsuspect")
     */
    public function unsuspect($id, PurchaseSuspicionRepository $suspicionRepository, EntityManagerInterface $manager){

        /** @var Purchase */
        $suspicion = $suspicionRepository->findBy(["purchase_id"=>$id]);

        if(!$suspicion){
            return;
        }

        $manager->remove($suspicion);
        $manager->flush();

        return;
    }
}