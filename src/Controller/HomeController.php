<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EntityManagerInterface $em/**, ProductRepository $productRepository */)
    {
        // $product=$productRepository->findOneBy(["slug"=>"chaise-en-bois"], ["name"=>"DESC"]);
        // dd($product);

        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->findBy([], [], 3);

        //$product = new Product;
        //$product->setPrice(3500);

        //$em->remove($product); => Pour supprimer un produit préalablement séléctionné
        //$em->persist($product); => Seulement si l'entité n'existe pas
        //$em->flush();
        

        return $this->render('home/home.html.twig', ["products"=>$products]);
    }
}
