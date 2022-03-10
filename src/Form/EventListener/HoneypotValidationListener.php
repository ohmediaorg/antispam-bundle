<?php

namespace OHMedia\AntispamBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Contracts\Translation\TranslatorInterface;

class HoneypotValidationListener implements EventSubscriberInterface
{
    private $fieldName;
    private $errorMessage;
    private $translator;
    private $translationDomain;
    private $serverParams;

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function __construct(
        string $fieldName,
        string $errorMessage,
        TranslatorInterface $translator = null,
        string $translationDomain = null,
        ServerParams $serverParams = null
    ) {
        $this->fieldName = $fieldName;
        $this->errorMessage = $errorMessage;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->serverParams = $serverParams ?: new ServerParams();
    }

    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $postRequestSizeExceeded = 'POST' === $form->getConfig()->getMethod() && $this->serverParams->hasPostMaxSizeBeenExceeded();

        if ($form->isRoot() && $form->getConfig()->getOption('compound') && !$postRequestSizeExceeded) {
            $data = $event->getData();

            $honeypotValue = \is_string($data[$this->fieldName] ?? null) ? $data[$this->fieldName] : null;

            if (null !== $honeypotValue && '' !== $honeypotValue) {
                $errorMessage = $this->errorMessage;

                if (null !== $this->translator) {
                    $errorMessage = $this->translator->trans($errorMessage, [], $this->translationDomain);
                }

                $form->addError(new FormError($errorMessage));
            }

            if (\is_array($data)) {
                unset($data[$this->fieldName]);
                $event->setData($data);
            }
        }
    }
}
