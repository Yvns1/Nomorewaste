<?php

namespace App\Form;

use App\Entity\CookingClass;
use App\Entity\Utilisateurs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CookingClassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('startTime')
            ->add('duration')
            ->add('maxParticipants')
            ->add('volunteer', EntityType::class, [
                'class' => Utilisateurs::class,
                'choices' => $options['volunteers'],
                'choice_label' => 'name',
                'label' => 'Volunteer',
                'placeholder' => 'Select a volunteer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CookingClass::class,
            'volunteers' => [], // Ajouter cette ligne pour passer les bénévoles en option
        ]);
    }
}
