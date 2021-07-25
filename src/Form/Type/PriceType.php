<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType{

    //Définis la construction (builder) pour le type de champs en fonction des options 
    // (valeur par défauts parent + actuel et valeur définis lors de l'appel du type de champs)
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options["divide"]===false){
            return;
        }
        $builder->addModelTransformer(new CentimesTransformer);
    }

    //Définis un type parent
    public function getParent()
    {
        return NumberType::class;
    }

    //On défini les paramètres par défauts (surcharge du type parent)
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "divide"=>true
        ]);
    }
}