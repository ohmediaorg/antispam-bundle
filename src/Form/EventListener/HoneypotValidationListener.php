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

    public static function getSubscribedEvents(): array
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

        $isMethodPost = 'POST' === $form->getConfig()->getMethod();

        if ($isMethodPost && $this->serverParams->hasPostMaxSizeBeenExceeded()) {
            return;
        }

        if (!$form->isRoot()) {
            return;
        }

        if (!$form->getConfig()->getOption('compound')) {
            return;
        }

        $data = $event->getData();

        $honeypotFields = \is_array($data[$this->fieldName])
            ? $data[$this->fieldName]
            : [];

        $isHoneypotFilled = false;

        foreach ($honeypotFields as $honeypotField => $honeypotValue) {
            if (null !== $honeypotValue && '' !== $honeypotValue) {
                $isHoneypotFilled = true;

                break;
            }
        }

        if ($isHoneypotFilled) {
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
