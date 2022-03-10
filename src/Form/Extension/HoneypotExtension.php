<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use Symfony\Component\Form\AbstractExtension;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This extension protects forms by using a hidden honeypot field.
 */
class HoneypotExtension extends AbstractExtension
{
    private $translator;
    private $translationDomain;

    public function __construct(TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadTypeExtensions()
    {
        return [
            new FormTypeHoneypotExtension($this->translator, $this->translationDomain),
        ];
    }
}
