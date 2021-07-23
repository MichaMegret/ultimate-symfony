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

        //$product = new Product;
        $productRepository = $em->getRepository(Product::class);
        $product = $productRepository->find(2);
        //$product->setPrice(3500);

        //$em->remove($product);

        //$em->persist($product); => Seulement si l'entité n'existe pas
        //$em->flush();

phpinfo();
        return $this->render('home/home.html.twig');
    }
}
