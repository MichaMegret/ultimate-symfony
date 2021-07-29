<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartService extends AbstractController{

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;   
        $this->productRepository = $productRepository;
    }


    protected function getCart(){
        return $this->session->get("cart", []);
    }

    protected function saveCart(array $cart){
        $this->session->set("cart", $cart);
    }



    public function add(int $id){

        $cart = $this->getCart();

        if(array_key_exists($id, $cart)){
            $cart[$id]++;
        }
        else{
            $cart[$id]=1;
        }

        $this->saveCart($cart);
    }


    public function getTotal(): float{

        $total = 0;

        if(!$this->getCart()){
            return $total;
        }

        foreach($this->getCart() as $idProduct=>$qty){
            $product = $this->productRepository->find($idProduct);
            if(!$product){
                continue;
            }
            $total+=$product->getPrice() * $qty;
        }

        return $total;

    }

    public function getTotalItem(int $id): float    
    {
        $total = 0;
        $cart = $this->getCart();
        $product = $this->productRepository->find($id);

        if(!$this->session->get("cart") || !$cart[$id] || !$product){
            return $total;
        }

        return $total = ($product->getPrice() * $cart[$id]);


    }
    
    
    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array{

        $detailedCart = [];

        if(!$this->session->get("cart")){
            return $detailedCart;
        }

        foreach($this->session->get("cart") as $idProduct=>$qty){
            $product = $this->productRepository->find($idProduct);
            if(!$product){
                continue;
            }
            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;

    }


    public function removeOne(int $id){
        $cart = $this->getCart();
        if(!$cart[$id]){
            return;
        }

        if($cart[$id]==1 || $cart[$id]<=0){
            $this->remove($id);
            return;
        }
        elseif($cart[$id]>0){
            $cart[$id]-=1;
        }

        $this->saveCart($cart);
    }


    public function remove(int $id){
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
        return !isset($cart[$id]);
    }


    public function empty(){
        $this->saveCart([]);
    }

}