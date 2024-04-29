<?php

namespace OHMedia\AntispamBundle\Twig;

use OHMedia\AntispamBundle\Form\Type\CaptchaType;
use OHMedia\AntispamBundle\OHMediaAntispamBundle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CaptchaExtension extends AbstractExtension
{
    private $rendered = false;
    private $isTypeHcaptcha;
    private $isTypeRecaptcha;

    public function __construct(
        #[Autowire('%oh_media_antispam.captcha.type')]
        private string $type,
        #[Autowire('%oh_media_antispam.captcha.sitekey')]
        private string $sitekey,
        #[Autowire('%oh_media_antispam.captcha.theme')]
        private string $theme,
        #[Autowire('%oh_media_antispam.captcha.size')]
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
