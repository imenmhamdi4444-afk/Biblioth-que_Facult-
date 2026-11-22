<?php

namespace App\Form;

use App\Entity\Publisher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublisherType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raisonSociale', TextType::class, [
                'label' => 'Raison sociale',
                'attr'  => ['class' => 'form-control'],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr'  => ['class' => 'form-control'],
            ])
            ->add('directorName', TextType::class, [
                'label' => 'Nom du directeur',
                'attr'  => ['class' => 'form-control'],
            ])
            ->add('commercialContact', TextType::class, [
                'label' => 'Responsable commercial',
                'attr'  => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publisher::class,
        ]);
    }
}
