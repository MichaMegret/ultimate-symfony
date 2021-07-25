<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            "slug" => $slug
        ]);
        
        if(!$category){var_dump((1));
            //throw new NotFoundHttpException("La catégorie $slug n'existe pas"); => Alternative
            throw $this->createNotFoundException("La catégorie n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository){

        $product = $productRepository->findOneBy([
            "slug"=>$slug
        ]);

        if(!$productRepository){
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product'=>$product/**,
            "urlGenerator"=>$urlGenerator*/
        ]);
    }


    /**
     *@Route("/admin/product/create", name="product_create") 
     */
    public function create(/**FormFactoryInterface $factory, */Request $request, Slugify $slugify, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator){

        //----------------------Méthode de création de formulaire avec le servie FormFactoryInterface-----------------------//

        // $builder = $factory->createBuilder(ProductType::class/**, null, [ ===> FormType par défaut
        //     'data_class'=>Product::class
        // ]*/);
        //
        //Pour changer le nom du formulaire, id etc...
        //$builder =  $factory->createNameBuilder('<FormName>', ProductType::class);
        //
        // $form = $builder->getForm();

        //--------------------Méthode de création de formulaire directement à partir de AbstractController-------------------//

        $form = $this->createForm(ProductType::class);

        // $builder->add("name", TextType::class, [
        //     "label"=>"Nom du produit",
        //     "attr"=>[
        //         //'class'=>"form-control mt-2 mb-2",
        //         "placeholder"=>"Saisissez le nom du produit"
        //         ]
        //     ])
        //     ->add("shortDescription", TextareaType::class, [
        //         "label"=>"Description du produit",
        //         "attr"=>[
        //             //"class"=>"form-control mt-2 mb-2",
        //             'placeholder'=>"Saisissez une description courte mais parlante"
        //         ]
        //     ])
        //     ->add("price", MoneyType::class, [
        //         "label"=>"Prix du produit",
        //         "attr"=>[
        //             //"class"=>"form-control mt-2 mb-2",
        //             "placeholder"=>"Saisissez le prix du produit"
        //         ]
        //     ])
        //     ->add("mainPicture", UrlType::class, [
        //         "label"=>"Image du produit",
        //         "attr"=>[
        //             "placeholder"=>"Entrez une URL d'image"
        //         ]
        //     ])
        //     ->add("category", EntityType::class, [
        //         "label"=>"Catégorie du produit",
        //         // "attr"=>[
        //         //     "class"=>"form-control mt-2 mb-2"
        //         // ],
        //         "placeholder"=>"-- Choisir une catégorie de produit --",
        //         "class"=>Category::class,
        //         "choice_label"=> function(Category $category){
        //             return strtoupper($category->getName());
        //         }
        //     ]);

        /**$options = [];

        foreach($categoryRepository->findAll() as $category){
            $options[$category->getName()] = $category->getId();
        }
            
        $builder->add("category", ChoiceType::class, [
                "label"=>"Catégorie du produit",
                "attr"=>[
                    "class"=>"form-control"
                ],
                "placeholder"=>"-- Choisir une catégorie de produit --",
                "choices"=>$options
            ]);*/

        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            $data = $form->getData();

            // $product = new Product;
            // $product
            //     ->setName($data["name"])
            //     ->setShortDescription($data["shortDescription"])   
            //     ->setPrice($data["price"] * 100)
            //     ->setCategory($data["category"]);
            // dd($product);
            // => Pas nécessaire si la class Product est rendu au formBuilder via l'option data_class 
            // => Dans ce cas $data contient automatiquement une entité product avec les infos fournies par getData

            $data->setSlug(strtolower($slugify->slugify($data->getName())));
            $em->persist($data);
            $em->flush();
            
            // $url = $urlGenerator->generate("product_show", [
            //     "category_slug"=>$data->getCategory()->getSlug(),
            //     "slug"=>$data->getSlug()
            // ]); ==> Vas avec redirect($url)

            // $response = new RedirectResponse($url);
            // return $response; ==> Simplifiable par redirect($url)

            //return $this->redirect($url);

            return $this->redirectToRoute("product_show", [
                    "category_slug"=>$data->getCategory()->getSlug(),
                    "slug"=>$data->getSlug()
            ]); //Méthode condencée
        }

        $formView = $form->createView();

        return $this->render("product/create.html.twig", [
            "formView" => $formView
        ]);
    }



    /**
     *@Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, Slugify $slugify){

        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
        //$form->setData($product);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            //$product = $form->getData(); => Pas nécessaire car apres handelRequest la variable product à été mis à jour

            $product->setSlug(strtolower($slugify->slugify($product->getName())));
            $em->flush(); //Pas besoin de persist() puisque product existai deja avant

            
            // $url = $urlGenerator->generate("product_show", [
            //     "category_slug"=>$product->getCategory()->getSlug(),
            //     "slug"=>$product->getSlug()
            // ]);
            
            //$response = new Response(); ==> Pour redirection mannuelle
            // $response->headers->set("Location", $url);
            // $response->setStatusCode(302); ==> Simplifiable par la class RedirectResponse ci après

            // $response = new RedirectResponse($url);

            // return $response;
            return $this->redirectToRoute("product_show", [
                "category_slug"=>$product->getCategory()->getSlug(),
                "slug"=>$product->getSlug()
        ]); //Méthode condencée
        }

        $formView = $form->createView();

        return $this->render("product/edit.html.twig", [
            "product"=>$product,
            "formView"=>$formView
        ]);
    }
}
