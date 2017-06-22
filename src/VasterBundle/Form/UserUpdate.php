<?php

namespace VasterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use VasterBundle\Entity\User;

class UserUpdate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accounttype', ChoiceType::class, [
                'choices'  => [
                    'Standard' => 'Standard',
                    'Internal' => 'Internal'
                ]
                //'placeholder' => 'Choose an option'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'vaster_bundle_user_update';
    }
}
