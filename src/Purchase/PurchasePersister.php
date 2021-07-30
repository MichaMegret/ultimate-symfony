<?php 

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePersister extends AbstractController{

    protected $cartService;
    protected $manager;

    public function __construct(CartService $cartService, EntityManagerInterface $manager)
    {
        $this->cartService = $cartService;
        $this->manager = $manager;
    }

    public function storePurchase(Purchase $purchase){

        $user = $this->getUser();

        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->manager->persist($purchase);

        foreach($this->cartService->getDetailedCartItems() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice())
                ->setProductName($cartItem->product->getName());

            $this->manager->persist($purchaseItem);
        }
        
        $this->manager->flush();

        //$this->cartService->empty();
    }
}