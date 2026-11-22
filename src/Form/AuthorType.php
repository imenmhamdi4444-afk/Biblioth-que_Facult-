<?php

namespace App\Form;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom', 'attr' => ['class' => 'form-control']])
            ->add('surname', TextType::class, ['label' => 'Prénom', 'attr' => ['class' => 'form-control']])
            ->add('workAddress', TextType::class, [
                'label'    => 'Adresse de travail',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('interest1', TextType::class, [
                'label'    => 'Centre d\'intérêt 1',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: Bases de données'],
            ])
            ->add('interest2', TextType::class, [
                'label'    => 'Centre d\'intérêt 2',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: Programmation'],
            ])
            ->add('interest3', TextType::class, [
                'label'    => 'Centre d\'intérêt 3',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: Méthodologie'],
            ])
            ->add('origin', TextType::class, [
                'label'    => 'Origine',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: Algérie, France…'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
