<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{

    protected $session;
    protected $cartService;
    protected $productRepository;


    public function __construct(SessionInterface $session, CartService $cartService, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/cart/add/{id}/{showMessage?1}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, $showMessage)
    {

        $product = $this->productRepository->find($id);

        if(!$product){
            //$this->addFlash("danger", "Le produit n'existe pas");
            //return $this->redirectToRoute("homepage");
            return $this->json([
                "type"=>"danger",
                "message"=>"Le produit n'existe pas",
                "code"=>"error"
            ]);
        }

        $this->cartService->add($id);

        $nbThisProduct = $this->session->get("cart")[$id];
        $totalAmountItem = $this->cartService->getTotalItem($id);


        return $this->json([
            "type"=>"success",
            "message"=>"Le produit à bien été ajouté",
            "showMessage"=>$showMessage,
            "code"=>"success",
            "cartAmount"=>$this->cartService->getTotal(),
            "cartItems"=>count($this->cartService->getDetailedCartItems()),
            "nbThisProduct"=>$nbThisProduct,
            "totalAmountItem"=>$totalAmountItem
        ]);

        /**@var FlashBag */
        // $flashBag=$session->getBag("flashes");
        // $flashBag->add("success", "Le produit à été ajouté au panier");
        // ==>Equivalent

        //$this->addFlash("success", "Le produit à été ajouté au panier");

        // if($redirect = $request->query->get("redirect")){
        //     if(strstr($request->query->get("redirect"), "/")){
        //         return $this->redirect($request->query->get("redirect"));
        //     }
        //     return $this->redirectToRoute($redirect);
        // }
            
        // return $this->redirectToRoute("product_show", [
        //     "category_slug"=>$product->getCategory()->getSlug(),
        //     "slug"=>$product->getSlug()
        // ]);
        
    }


    /**
     * @Route("/cart/decrement/{id<\d+>}/{showMessage?1}", name="cart_decrement")
     */
    public function decrement($id, $showMessage)
    {
        $product = $this->productRepository->find($id);
        if(!$product){
            // $this->addFlash("danger", "Le produit n'existe pas");
            return $this->json([
                "type"=>"danger",
                "message"=>"Le produit n'existe pas",
                "code"=>"error"
            ]);
        }

        $this->cartService->removeOne($id);
        
        $cartAmount = $this->cartService->getTotal()?$this->cartService->getTotal():0;
        $cartItems = !empty($this->cartService->getDetailedCartItems())?count($this->cartService->getDetailedCartItems()):0;
        $nbThisProduct = isset($this->session->get("cart")[$id])?$this->session->get("cart")[$id]:0;
        $totalAmountItem = $this->cartService->getTotalItem($id)>0.01?$this->cartService->getTotalItem($id):0;

        return $this->json([
            "type"=>"success",
            "message"=>"Le produit à bien été décrémenté",
            "showMessage"=>$showMessage,
            "code"=>"success",
            "cartAmount"=>$cartAmount,
            "cartItems"=>$cartItems,
            "nbThisProduct"=>$nbThisProduct,
            "totalAmountItem"=>$totalAmountItem
        ]);
    }


    /**
     * @Route("/cart/show", name="cart_show")
     */
    public function show(){
        dump($this->session->get("cart"));
        $form = $this->createForm(CartConfirmationType::class);

        $this->session->set("tryToConnectRoute", $this->session->get("urlOrigine"));

        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();
        
        return $this->render("cart/show.html.twig", [
            "items"=>$detailedCart,
            "total"=>$total,
            "confirmationForm"=>$form->createView()
        ]);
    }


    /**
     * @Route("/cart/delete/{id<\d+>}", name="cart_delete")
     */
    public function delete($id){
        $product = $this->productRepository->find(($id));

        if(!$product){
            $this->addFlash("danger", "Le produit $id n'existe pas et ne peut donc pas être supprimé");
        }

        if($this->cartService->remove($id)){
            $this->addFlash("success", "Le produit à bien été supprimé");
        }
        else{
            $this->addFlash("danger", "Une erreur est survenue lors de la suppression. Veuillez réessayer.");
        }
        return $this->redirectToRoute("cart_show");

    }
}
