<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\DataTransformer\CentimesTransformer;
use App\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use function PHPUnit\Framework\isNull;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class, [
                "label" => "Nom du produit",
                "attr" => [
                    //'class'=>"form-control mt-2 mb-2",
                    "placeholder" => "Saisissez le nom du produit"
                ]
            ])
            ->add("shortDescription", TextareaType::class, [
                "label" => "Description du produit",
                "attr" => [
                    //"class"=>"form-control mt-2 mb-2",
                    'placeholder' => "Saisissez une description courte mais parlante"
                ]
            ])
            // ->add("price", PriceType::class, [
            //         "label" => "Prix du produit",
            //         "attr" => [
            //             //"class"=>"form-control mt-2 mb-2",
            //             "placeholder" => "Saisissez le prix du produit"
            //         ],
            //         "divide"=>true
            // ])
            ->add("price", MoneyType::class, [
                "label" => "Prix du produit",
                "attr" => [
                    //"class"=>"form-control mt-2 mb-2",
                    "placeholder" => "Saisissez le prix du produit"
                ],
                "divisor"=>100
            ]) //Admettons que MoneyType n'existe pas nous allons créer notre propre type de champs
            ->add("mainPicture", UrlType::class, [
                "label" => "Image du produit",
                "attr" => [
                    "placeholder" => "Entrez une URL d'image"
                ]
            ])
            ->add("category", EntityType::class, [
                "label" => "Catégorie du produit",
                // "attr"=>[
                //     "class"=>"form-control mt-2 mb-2"
                // ],
                "placeholder" => "-- Choisir une catégorie de produit --",
                "class" => Category::class,
                "choice_label" => function (Category $category) {
                    return $category->getName();
                }
            ]);

        //addModelTransformer() attend une classe qui implémente la classe DataTransformerInterface
        //Nous lui passons CallbackTransformer , native de symfony qui implémente déjà DataTransformerInterface
        // qui attend deux fonctions pour le constructeur, la première représente la transformation de la donnée avant affichage à utilisateur
        // et la seconde 'la reverse function) s'occupe du traitement de la donnée renseignée par utilisateur avant enregistrement
        // $builder->get("price")->addModelTransformer(new CallbackTransformer(
        //     function($value){
        //         if($value===null){
        //             return;
        //         }
        //         return $value/100;
        //     },
        //     function($value){
        //         if($value===null){
        //             return;
        //         }
        //         return $value*100;
        //     }
        // ));

        // On peut définir ce comportement pour plusieurs formulaire en implémentant une classe qui implémente DataTransformerInterface
        // Nous le faisons ici en utilisant la classe App\Form\DataTransformer\CentimesTransformer.php que nous avons créé
        // Tout ceci est commenté étant donné que la class MoneyType possède nativement cettefonctionnalité avec divisor

        // $builder->get("price")->addModelTransformer(new CentimesTransformer);
        

        /**--------------------------------------------------------------------------------------------------------------------
         *  Exemple d'adaptation du formulaire au type d'évenement ci-dessous.
         * On remarque que l'on peut également adapté les donnée selon les différentes étapes du builder
         * Ici on modifie l'affichage du prix pour présenter des euros à l'utilisateur et garder un nombre entier de centimes dans la base
         * 
         * Etant donné qu'il s'agit de manipulation de donnée et non pas du formulaire lui-même,
         * On préferera utiliser addModelTransformer() sur l'attribut price du produit comme ci dessus
         * 
         * addModelTransformer() => Interviens au moment ou on passe les donnée et ou on va les inserer dans l'objet form
         * addViewTransformer() => Interviens au moment ou l'objet form vas afficher les données dans le formulaire
          --------------------------------------------------------------------------------------------------------------------*/

        //$builder->addEventListener("form.pre_set_data") => Avant le positionnement des données dans le formulaire
        //$builder->addEventListener("form.post_set_data") => Après le positionnement des données dans le formulaire
        //$builder->addEventListener("form.pre_submit") => Avant la soumission du formulaire (Analyse de la requête)
        //$builder->addEventListener("form.submit") => Les données sont prêtes à être intégrées dans le formulaire
        //$builder->addEventListener("form.post_submit") => Les données sont intégrées dans le formulaire

        // Méthode moderne par appel de classe 
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
        //     $form = $event->getForm();

        //     /** @var Product */
        //     $product = $event->getData();

        //     if($product!==null){
        //         $product->setPrice($product->getPrice() / 100);
        //     }

            // Ici on veut que la catégorie ne soit pas modifiable pour un produit existant 
            //-----------------------------------------------------------------------------------------------------------------
            //Il faut penser à enlever cet élément des add() du $builder et dans le twig du formulaire 
            // Si le produit vaut null c'est que nous sommes sur la page de création
            // if(!$product){
            //     $form
            //         ->add("category", EntityType::class, [
            //             "label" => "Catégorie du produit",
            //             // "attr"=>[
            //             //     "class"=>"form-control mt-2 mb-2"
            //             // ],
            //             "placeholder" => "-- Choisir une catégorie de produit --",
            //             "class" => Category::class,
            //             "choice_label" => function (Category $category) {
            //                 return $category->getName();
            //             }
            //         ]);
            // }
    // }); 

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){

        //     /** @var Product */
        //     $product = $event->getData();

        //     if($product!==null){
        //         $product->setPrice($product->getPrice() * 100);
        //     }
        // });
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
