<?php

namespace App\Form;

use App\Entity\Agent;
use App\Entity\Category;
use App\Entity\Property;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
            ->add('imageFile', FileType::class, [
                'label' => 'Property Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                    ])
                ],
            ])
            ->add('galleryFiles', FileType::class, [
                'label' => 'Gallery Images',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\All([
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                            'mimeTypesMessage' => 'Please upload valid images (JPEG, PNG, WEBP)',
                        ])
                    ])
                ],
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
