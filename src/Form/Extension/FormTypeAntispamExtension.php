<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use OHMedia\AntispamBundle\Form\EventListener\AntispamValidationListener;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\RequestStack;

class FormTypeAntispamExtension extends AbstractTypeExtension
{
    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private ?ServerParams $serverParams,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AntispamValidationListener(
                $this->requestStack,
                $this->security,
                $this->serverParams,
            ))
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
