<?php

namespace App\Form;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('address', TextType::class)
            ->add('city', TextType::class, ['required' => false])
            ->add('state', TextType::class, ['required' => false])
            ->add('price', NumberType::class)
            ->add('area', NumberType::class)
            ->add('bedrooms', NumberType::class)
            ->add('bathrooms', NumberType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->add('image', TextType::class, [
                'help' => 'Path or URL to the image (e.g. images/japan.png)'
            ])
            ->add('listingType', ChoiceType::class, [
                'choices'  => [
                    'For Sale' => 'sale',
                    'For Rent' => 'rent',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Available' => 'available',
                    'Pending' => 'pending',
                    'Sold' => 'sold',
                    'Rented' => 'rented',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
        ]);
    }
}
