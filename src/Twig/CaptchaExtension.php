<?php

namespace OHMedia\AntispamBundle\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchaExtension extends AbstractExtension
{
    public const JS_PREFIX = 'ohmedia_antispam_bundle_captcha_';
    public const JS_CALLBACK = self::JS_PREFIX.'callback';
    public const JS_BOOLEAN = self::JS_PREFIX.'boolean';
    public const JS_PROMISE = self::JS_PREFIX.'promise';

    private $rendered = false;
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('captcha_script', [$this, 'script'], [
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

        return $twig->render('@OHMediaAntispam/captcha_script.html.twig', [
            'callback' => self::JS_CALLBACK,
            'boolean' => self::JS_BOOLEAN,
            'promise' => self::JS_PROMISE,
            'type' => $this->type,
        ]);
    }
}
