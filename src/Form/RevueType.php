<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Publisher;
use App\Entity\Revue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RevueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'Code document (Laisser vide pour auto)',
                'required' => false,
                'attr'  => ['class' => 'form-control', 'placeholder' => 'ex: REV-2024-001'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['class' => 'form-control'],
            ])
            ->add('publicationDate', DateType::class, [
                'label'    => 'Date de parution',
                'widget'   => 'single_text',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('publisher', EntityType::class, [
                'label'        => 'Maison d\'édition',
                'class'        => Publisher::class,
                'choice_label' => 'raisonSociale',
                'required'     => false,
                'placeholder'  => '-- Choisir une maison d\'édition --',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('authors', EntityType::class, [
                'label'        => 'Auteur(s)',
                'class'        => Author::class,
                'choice_label' => fn (Author $a) => $a->getName() . ' ' . $a->getSurname(),
                'multiple'     => true,
                'expanded'     => false,
                'required'     => false,
                'attr'         => ['class' => 'form-select', 'size' => 5],
            ])
            ->add('nbCopiesAcquired', IntegerType::class, [
                'label' => 'Exemplaires acquis',
                'attr'  => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('nbCopiesBorrowed', IntegerType::class, [
                'label'      => 'Exemplaires prêtés',
                'required'   => false,
                'empty_data' => 0,
                'attr'       => ['class' => 'form-control', 'min' => 0],
            ])
            ->add('keywords', TextType::class, [
                'label'    => 'Mots clés',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: informatique, réseau'],
            ])
            ->add('periodicity', TextType::class, [
                'label'    => 'Périodicité',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'placeholder' => 'ex: Mensuelle, Trimestrielle'],
            ])
            ->add('subscriptionDate', DateType::class, [
                'label'    => 'Date d\'abonnement',
                'widget'   => 'single_text',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('subscriptionAmount', MoneyType::class, [
                'label'    => 'Montant d\'abonnement',
                'currency' => 'DZD',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de couverture (JPG/PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG ou PNG)',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Revue::class,
        ]);
    }
}
