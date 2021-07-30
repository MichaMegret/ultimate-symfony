<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Cocur\Slugify\Slugify;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
    /**
     * @Route("/category/{slug}", name="product_category", priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository, Request $request, SessionInterface $session): Response
    {
        // $session->clear();
        dump($session);
        $category = $categoryRepository->findOneBy([
            "slug" => $slug
        ]);
        
        if(!$category){
            $request->getSession()->set("messageError", "La catégorie n'existe pas");
            return $this->redirectToRoute("homepage");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
            "origine" => "/".$slug
        ]);
    }






    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show($slug, $prenom, ProductRepository $productRepository, Request $request){

        // dd($prenom);
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
        
        if($form->isSubmitted() && $form->isValid()){
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
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, Slugify $slugify,
    ValidatorInterface $validator){


        //-------------------------------------------------------------------------------------------------------------------------
        // Validation de donnée simple
        //-------------------------------------------------------------------------------------------------------------------------
        
        // $age=20;
        // $resultat = $validator->validate($age, [
        //     new LessThan([
        //         "value"=>120,
        //         "message"=>"L'âge doit être inferieur à {{ value }} mais vous avez saisie {{ compared_value }}"
        //     ]),
        //     new GreaterThan([
        //         "value"=>0,
        //         "message"=>"L'âge doit être supérieur à 0"
        //     ])
        // ]);

        // if($resultat->count() > 0){
        //     dd("Il y a des erreurs", $resultat);
        // }

        // dd("Tout vas bien");


        //-------------------------------------------------------------------------------------------------------------------------
        // Validation de tableaux associatifs
        //-------------------------------------------------------------------------------------------------------------------------

        // $client = [
        //     "nom"=>"Megret",
        //     "prenom"=>"Micha",
        //     "voiture"=> [
        //         "marque"=>"Citroëna",
        //         "couleur"=>"Blanche"
        //     ]
        // ];


        // $collection = new Collection([
        //     "nom"=>new NotBlank(["message"=>"Le nom ne doit pas être vide"]),
        //     "prenom"=>[
        //         new NotBlank(["message"=>"Le prénom ne doit pas être vide"]),
        //         new Length([
        //             "min"=>3,
        //             "max"=>25,
        //             "minMessage"=>"Le prénom doit comporter au moins 3 caractères",
        //             "maxMessage"=>"Le prénom ne doit pas dépasser les 25 caractères"
        //         ])
        //         ],
        //         "voiture"=>new Collection([
        //             "marque"=>new Choice([
        //                 "choices"=>["Citroën", "Renault"],
        //                 "message"=>"La marque doit être renseignée. Choix : {{ choices }}"
        //             ]),
        //             "couleur"=>new NotBlank(["message"=>"La couleur doit être renseignée"])
        //         ])
        // ]);

        // $resultat = $validator->validate($client, $collection);
        // dd($resultat);


        //-------------------------------------------------------------------------------------------------------------------------
        // Validation d'objet en yaml (voir config/validator/validator_product.yaml)
        //----------------------------------------------------------------------------------------------------------------------

        // $product = new Product;

        // $product->setName("Verre en bois");
        // $product->setPrice(1200);

        // $resultat = $validator->validate($product);

        // dd($resultat);



        //-------------------------------------------------------------------------------------------------------------------------
        // Validation d'objet en php, ici entité Product (via la méthode loadValidatorMetadata() remplacé par les @Assert de l'entité Product)
        //----------------------------------------------------------------------------------------------------------------------

        // $product = new Product;

        // $product->setName("Verre en bois");
        // $product->setPrice(11000);

        // $resultat = $validator->validate($product);

        // dd($resultat);

        //-------------------------------------------------------------------------------------------------------------------------
        // ??? Il est possible de spécifier la validation d'un champ en particulier depuis le Type du formulaire avec constraints()
        //----------------------------------------------------------------------------------------------------------------------
       

        //-------------------------------------------------------------------------------------------------------------------------
        // Validation en tenant compte des groupes de validation, Defaut représente les validations sans groupe défini
        // Equivalent à "validation_groups" sur le formulaire
        //----------------------------------------------------------------------------------------------------------------------
        // $product = new Product;
        // $resultat = $validator->validate($product, null, ["Default", "with-price"]);
        // dd($resultat);


        //-------------------------------------------------------------------------------------------------------------------------
        //Traitement et affichage du formulaire
        //----------------------------------------------------------------------------------------------------------------------
       
        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product, [
            "validation_groups"=>[
                "with-price",
                "Default"
            ]
        ]);
        //$form->setData($product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
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
