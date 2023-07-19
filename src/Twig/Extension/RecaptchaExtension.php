<?php

namespace OHMedia\AntispamBundle\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RecaptchaExtension extends AbstractExtension
{
    const JS_PREFIX = 'ohmedia_antispam_bundle_recaptcha_';
    const JS_CALLBACK = self::JS_PREFIX . 'callback';
    const JS_BOOLEAN = self::JS_PREFIX . 'boolean';
    const JS_PROMISE = self::JS_PREFIX . 'promise';

    private $rendered = false;

    public function getFunctions(): array
    {
        return [
            new TwigFunction('recaptcha_script', [$this, 'script'], [
                'is_safe' => ['html'],
                'needs_environment' => 'true',
            ]),
        ];
    }

    public function script(Environment $twig)
    {
        if ($this->rendered) {
            return '';
        }

        $this->rendered = true;

        return $twig->render('@OHMediaAntispam/recaptcha_script.html.twig', [
            'callback' => self::JS_CALLBACK,
            'boolean' => self::JS_BOOLEAN,
            'promise' => self::JS_PROMISE,
        ]);
    }
}
