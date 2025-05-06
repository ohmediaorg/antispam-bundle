<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use OHMedia\AntispamBundle\Form\EventListener\ThrottleValidationListener;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormTypeThrottleExtension extends AbstractTypeExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private ?ServerParams $serverParams,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['throttle_protection']) {
            return;
        }

        $builder
            ->addEventSubscriber(new ThrottleValidationListener(
                $this->requestStack,
                $this->security,
                $options['throttle_time'],
                $options['throttle_window'],
                $this->serverParams,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'throttle_protection' => true,
            'throttle_time' => 5,
            'throttle_window' => 300,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
