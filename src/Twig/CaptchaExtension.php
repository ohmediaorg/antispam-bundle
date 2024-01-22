<?php

namespace OHMedia\AntispamBundle\Twig;

use OHMedia\AntispamBundle\DependencyInjection\Configuration;
use OHMedia\AntispamBundle\Form\Type\CaptchaType;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchaExtension extends AbstractExtension
{
    private $rendered = false;
    private $typeHcaptcha;
    private $typeRecaptcha;
    private $sitekey;
    private $theme;
    private $size;

    public function __construct(string $type, string $sitekey, string $theme, string $size)
    {
        $this->typeHcaptcha = Configuration::CAPTCHA_TYPE_HCAPTCHA === $type;
        $this->typeRecaptcha = Configuration::CAPTCHA_TYPE_RECAPTCHA === $type;
        $this->sitekey = $sitekey;
        $this->theme = $theme;
        $this->size = $size;
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
            'DATA_ATTRIBUTE' => CaptchaType::DATA_ATTRIBUTE,
            'typeHcaptcha' => $this->typeHcaptcha,
            'typeRecaptcha' => $this->typeRecaptcha,
            'sitekey' => $this->sitekey,
            'theme' => $this->theme,
            'size' => $this->size,
        ]);
    }
}
