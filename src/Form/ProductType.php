<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
            ->add("price", MoneyType::class, [
                "label" => "Prix du produit",
                "attr" => [
                    //"class"=>"form-control mt-2 mb-2",
                    "placeholder" => "Saisissez le prix du produit"
                ]
            ])
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
