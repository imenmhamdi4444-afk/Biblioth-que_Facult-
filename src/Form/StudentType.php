<?php

namespace App\Form;

use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire.']),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('surname', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire.']),
                    new Length(['max' => 255]),
                ],
            ])
            ->add('matricule', TextType::class, [
                'label'    => 'Matricule',
                'required' => false,
                'attr'     => ['class' => 'form-control mb-3', 'placeholder' => 'ex: 20210001'],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Email',
                'required' => false,
                'attr'     => ['class' => 'form-control mb-3', 'placeholder' => 'ex: etudiant@univ.dz'],
            ])
            ->add('department', TextType::class, [
                'label'    => 'Filière / Département',
                'required' => false,
                'attr'     => ['class' => 'form-control mb-3', 'placeholder' => 'ex: Informatique'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
