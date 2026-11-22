<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\Revue;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BorrowingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Only admins see the student dropdown — regular users are auto-assigned
        if ($options['is_admin']) {
            $builder->add('student', EntityType::class, [
                'class'        => Student::class,
                'choice_label' => function (Student $student) {
                    $label = $student->getName() . ' ' . $student->getSurename();
                    if ($student->getMatricule()) {
                        $label .= ' (' . $student->getMatricule() . ')';
                    }
                    return $label;
                },
                'label'       => 'Étudiant',
                'placeholder' => 'Sélectionnez un étudiant',
                'attr'        => ['class' => 'form-select mb-3'],
            ]);
        }

        $builder
            ->add('dateBorrowed', DateTimeType::class, [
                'widget' => 'single_text',
                'label'  => 'Date du prêt',
                'attr'   => ['class' => 'form-control mb-3'],
            ])
            ->add('book', EntityType::class, [
                'class'        => Book::class,
                'choice_label' => 'title',
                'label'        => 'Livre',
                'required'     => false,
                'placeholder'  => 'Sélectionnez un livre (ou une revue)',
                'attr'         => ['class' => 'form-select mb-3'],
            ])
            ->add('revue', EntityType::class, [
                'class'        => Revue::class,
                'choice_label' => 'title',
                'label'        => 'Revue',
                'required'     => false,
                'placeholder'  => 'Sélectionnez une revue (ou un livre)',
                'attr'         => ['class' => 'form-select mb-3'],
            ])
            ->add('bookReturned', CheckboxType::class, [
                'label'      => 'Document retourné ?',
                'required'   => false,
                'attr'       => ['class' => 'form-check-input ms-2'],
                'label_attr' => ['class' => 'form-check-label'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Borrowing::class,
            'is_admin'   => false,
        ]);

        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}
