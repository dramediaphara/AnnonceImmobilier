<?php

namespace App\Form;

use App\Entity\Tag;
use App\Entity\Chambre;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de Chambre',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                ]

            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class, //classe Entity utilisé pour notre champ
                'choice_label' => 'name', //Atribut utilisé pour representer
                'expanded' => false, //Affichage menu déroulant
                'multiple' => false, //On ne selectionner qu'une seule category
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                ]

            ])
            
            ->add('description', TextareaType::class, [
                'label' => 'Description ',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                ]

            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                ]

            ])
            ->add('tags', EntityType::class, [
                'label' => 'Tags',
                'class' => Tag::class,      //Classe Entity utilisé pour notre champ
                'choice_label' => 'name',   //Attribut utilisé pour représenter l'Entity
                'expanded' => true,         //Affichage cases
                'multiple' => true,         //Nous pouvons sélectionner PLUSIEURS Tags
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'w3-input w3-border w3-round w3-light-grey',
                    'style' => 'margin-top:10px;'
                ]

            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}

// D14N7N6L20S14@drame
