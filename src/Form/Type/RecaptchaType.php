<?php

namespace OHMedia\AntispamBundle\Form\Type;

use OHMedia\AntispamBundle\Validator\Constraints\Recaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecaptchaType extends AbstractType
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
            'recaptcha' => [
                'sitekey' => $this->sitekey,
                'theme' => $this->theme,
                'size' => $this->size
            ],
            'constraints' => [
                new Recaptcha()
            ]
        ]);
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('recaptcha', $options['recaptcha']);
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['recaptcha'] = $options['recaptcha'];
    }

    public function getParent()
    {
        return TextType::class;
    }
}
