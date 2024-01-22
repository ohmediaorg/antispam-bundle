<?php

namespace OHMedia\AntispamBundle\Form\Type;

use OHMedia\AntispamBundle\Validator\Constraints\Captcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptchaType extends AbstractType
{
    public const DATA_ATTRIBUTE = 'data-ohmedia-antispam-captcha';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'theme' => null,
            'size' => null,
            'constraints' => [
                new Captcha(),
            ],
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('theme', $options['theme']);
        $builder->setAttribute('size', $options['size']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['theme'] = $options['theme'];
        $view->vars['size'] = $options['size'];
        $view->vars['DATA_ATTRIBUTE'] = self::DATA_ATTRIBUTE;
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
