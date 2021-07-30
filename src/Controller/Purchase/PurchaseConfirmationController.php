<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\AccessManager\AccessBlocker;
use App\Entity\PurchaseItem;
use App\Purchase\PurchasePersister;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController{

    protected $formFactory;
    protected $cartService;
    protected $manager;
    protected $persister;

    public function __construct(FormFactoryInterface $formFactory, CartService $cartService, EntityManagerInterface $manager, PurchasePersister $persister)
    {
        $this->formFactory = $formFactory;
        $this->cartService = $cartService;
        $this->manager = $manager;
        $this->persister = $persister;
    }

    /**
     * @Route("purchase/confirm", name="purchase_confirm", priority=1)
     */
    public function confirm(Request $request, AccessBlocker $accessBlocker){

        // Si l'utilisateur n'est pas connecté 
        if($blocker = $accessBlocker->redirect_noUser("Vous devez être connecté pour valider une commande")){
            return $blocker;
        }
            
        // Récupération du formulaire
        //$form = $this->formFactory->create(CartConfirmationType::class);
        $form = $this->createForm(CartConfirmationType::class);

        // Vérifie la soumission du formulaire
        $form->handleRequest($request);

        // Redirection si le formulaire de confirmation n'est pas rempli
        if(!$form->isSubmitted()){
            $this->addFlash("danger", "Vous devez remplir le formulaire de confirmation au préalable");
            return $this->redirectToRoute("cart_show");
        }

        // Récupération des éléments du panier
        $cartItems = $this->cartService->getDetailedCartItems();

        // Redirection si le panier est vide
        if(!count($cartItems)){
            $this->addFlash("danger", "Votre panier est vide, aucune commande en attente de confirmation");
            $this->redirectToRoute("cart_show");
        }
        
        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);
        $this->cartService->empty();

        //$this->addFlash("success", "La commande à bien été enregistrée");

        return $this->redirectToRoute("purchase_payment_form", [
            "id"=>$purchase->getId()
        ]);
    }

}