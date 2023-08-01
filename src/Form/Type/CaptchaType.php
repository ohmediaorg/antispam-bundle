<?php

namespace OHMedia\AntispamBundle\Form\Type;

use OHMedia\AntispamBundle\Twig\Extension\CaptchaExtension;
use OHMedia\AntispamBundle\Validator\Constraints\Captcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptchaType extends AbstractType
{
    private $sitekey;
    private $theme;
    private $size;

    public function __construct($sitekey, $theme, $size)
    {
        $this->sitekey = $sitekey;
        $this->theme = $theme;
        $this->size = $size;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'captcha' => [
                'sitekey' => $this->sitekey,
                'theme' => $this->theme,
                'size' => $this->size,
            ],
            'constraints' => [
                new Captcha(),
            ],
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('captcha', $options['captcha']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['captcha'] = $options['captcha'];

        $view->vars['promise'] = CaptchaExtension::JS_PROMISE;
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
