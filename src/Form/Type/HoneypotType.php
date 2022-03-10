<?php

namespace OHMedia\AntispamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HoneypotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributes = [
            'tabindex' => '-1',
            'autocomplete' => 'new-password',
        ];

        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attributes' => $attributes,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attributes' => $attributes,
            ])
            ->add('comments', TextareaType::class, [
                'required' => false,
                'attributes' => $attributes,
            ])
        ;
    }
}
