<?php

namespace OHMedia\AntispamBundle\Twig;

use OHMedia\AntispamBundle\Form\Type\CaptchaType;
use OHMedia\AntispamBundle\OHMediaAntispamBundle;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchaExtension extends AbstractExtension
{
    private $rendered = false;
    private $isTypeHcaptcha;
    private $isTypeRecaptcha;

    public function __construct(
        private string $type,
        private string $sitekey,
        private string $theme,
        private string $size
    ) {
        $this->isTypeHcaptcha = OHMediaAntispamBundle::CAPTCHA_TYPE_HCAPTCHA === $type;
        $this->isTypeRecaptcha = OHMediaAntispamBundle::CAPTCHA_TYPE_RECAPTCHA === $type;
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
            'is_type_hcaptcha' => $this->isTypeHcaptcha,
            'is_type_recaptcha' => $this->isTypeRecaptcha,
            'sitekey' => $this->sitekey,
            'theme' => $this->theme,
            'size' => $this->size,
        ]);
    }
}
