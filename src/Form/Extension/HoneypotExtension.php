<?php

namespace OHMedia\AntispamBundle\Form\Extension;

use Symfony\Component\Form\AbstractExtension;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This extension protects forms by using a hidden honeypot field.
 */
class HoneypotExtension extends AbstractExtension
{
    public function __construct(
        private TranslatorInterface $translator = null,
        private string $translationDomain = null
    ) {
    }

    protected function loadTypeExtensions(): array
    {
        return [
            new FormTypeHoneypotExtension($this->translator, $this->translationDomain),
        ];
    }
}
