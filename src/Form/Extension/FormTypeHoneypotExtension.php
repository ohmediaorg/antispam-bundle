<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use OHMedia\AntispamBundle\Form\EventListener\HoneypotValidationListener;
use OHMedia\AntispamBundle\Form\Type\HoneypotType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeHoneypotExtension extends AbstractTypeExtension
{
    public function __construct(
        private ?ServerParams $serverParams
    ) {
    }

    /**
     * Adds a honeypot field to the form when the honeypot protection is enabled.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['honeypot_protection']) {
            return;
        }

        $builder
            ->addEventSubscriber(new HoneypotValidationListener(
                $options['honeypot_field_name'],
                $options['honeypot_message'],
                $this->serverParams
            ))
        ;
    }

    /**
     * Adds a honeypot field to the root form view.
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$options['honeypot_protection']) {
            return;
        }

        if ($view->parent) {
            return;
        }

        if (!$options['compound']) {
            return;
        }

        $factory = $form->getConfig()->getFormFactory();

        $honeypotForm = $factory->createNamed(
            $options['honeypot_field_name'],
            HoneypotType::class,
            null,
            [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'style' => 'position: absolute;left:-5000px;',
                    'aria-hidden' => 'true',
                ],
                'row_attr' => [
                    'class' => 'fieldset-nostyle',
                ],
            ]
        );

        $view->children[$options['honeypot_field_name']] = $honeypotForm->createView($view);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'honeypot_protection' => false,
            'honeypot_field_name' => '_topyenoh',
            'honeypot_message' => 'Something went wrong!',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
