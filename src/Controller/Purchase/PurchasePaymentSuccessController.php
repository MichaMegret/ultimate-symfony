<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\AccessManager\AccessBlocker;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController{


    /**
     * @Route("/purchase/valide/{id<\d+>}", name="purchase_payment_success")
     */
    public function success(
        $id,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $manager,
        CartService $cartService,
        AccessBlocker $accessBlocker,
        EventDispatcherInterface $dispatcher
    ) {

        // Si l'utilisateur n'est pas connecté 
        if($blocker = $accessBlocker->redirect_noUser("Vous devez être connecté pour valider une commande")){
            return $blocker;
        }

        $purchase = $purchaseRepository->find(($id));

        if (
            !$purchase ||
            ($purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatut() === Purchase::STATUT_PAID)
        ) {
            $this->addFlash("warning", "La commande n'existe pas");
            $this->redirectToRoute("purchase_index");
        }

        $purchase->setStatut(Purchase::STATUT_PAID);
        $manager->flush();

        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, "purchase.success");

        //$this->addFlash("success", "La commande à bien été payée et sera traitée dans les meilleurs délais");

        return $this->redirectToRoute("purchase_index");

    }
}