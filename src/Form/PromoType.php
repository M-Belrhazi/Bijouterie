<?php

namespace App\Form;

use App\Entity\Promo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PromoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class ,[
                "required"=>false,
                "label"=>false,
                "attr"=>[
                "placeholder"=>"Saisir le nom"
                ]
            ])
            
            ->add('remise', NumberType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                "placeholder"=>"Saisir la remise"
                ]
            ])

            ->add('montantmin', NumberType::class, [
                "required"=>false,
                "label"=>false,
                "attr"=>[
                "placeholder"=>"Saisir la remise"
                ]
            ])
            
            ->add('Valider', SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promo::class,
        ]);
    }
}
