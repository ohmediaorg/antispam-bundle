<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use OHMedia\AntispamBundle\Form\EventListener\HoneypotValidationListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormTypeHoneypotExtension extends AbstractTypeExtension
{
    private $translator;
    private $translationDomain;
    private $serverParams;

    public function __construct(
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        ServerParams $serverParams = null
    ) {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->serverParams = $serverParams;
    }

    /**
     * Adds a honeypot field to the form when the honeypot protection is enabled.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['honeypot_protection']) {
            return;
        }

        $builder
            ->addEventSubscriber(new HoneypotValidationListener(
                $options['honeypot_field_name'],
                $options['honeypot_message'],
                $this->translator,
                $this->translationDomain,
                $this->serverParams
            ))
        ;
    }

    /**
     * Adds a honeypot field to the root form view.
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['honeypot_protection'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getFormFactory();

            $honeypotForm = $factory->createNamed($options['honeypot_field_name'], 'OHMedia\AntispamBundle\Form\Type\HoneypotType', null, [
                'mapped' => false,
            ]);

            $view->children[$options['honeypot_field_name']] = $honeypotForm->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'honeypot_protection' => true,
            'honeypot_field_name' => '_topyenoh',
            'honeypot_message' => 'Do not fill in the hidden field unless you want to look like a bot!',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
