<?php

namespace OHMedia\AntispamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class HoneypotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attr = [
            'tabindex' => '-1',
            'autocomplete' => 'new-password',
        ];

        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => $attr,
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'attr' => $attr,
            ])
            ->add('comments', TextareaType::class, [
                'required' => false,
                'attr' => $attr,
            ])
        ;
    }
}
