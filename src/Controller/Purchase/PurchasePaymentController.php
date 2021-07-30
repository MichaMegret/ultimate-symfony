<?php 

namespace App\Controller\Purchase;

use App\AccessManager\AccessBlocker;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController{

 
    /**
     * @Route("/purchase/pay/{id<\d+>}", name="purchase_payment_form")
     */
    public function showPaymentForm($id, PurchaseRepository $purchaseRepository, AccessBlocker $accessBlocker, StripeService $stripeService){

        // Si l'utilisateur n'est pas connecté 
        if($blocker = $accessBlocker->redirect_noUser("Vous devez être connecté pour valider une commande")){
            return $blocker;
        }

        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatut() === Purchase::STATUT_PAID)
        ) {
            $this->addFlash("warning", "La commande n'existe pas ou est déjà payée");
            $this->redirectToRoute("purchase_index");
        }

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render("purchase/payment.html.twig", [
            "clientSecret" => $intent->client_secret,
            "purchase"=>$purchase,
            "stripePublicKey"=>$stripeService->getPublicKey()
        ]);
    }
}