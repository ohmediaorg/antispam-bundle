<?php

namespace OHMedia\AntispamBundle\Validator;

use OHMedia\AntispamBundle\OHMediaAntispamBundle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CaptchaValidator extends ConstraintValidator
{
    private $url;

    public function __construct(
        private RequestStack $requestStack,
        #[Autowire('%oh_media_antispam.captcha.secretkey%')]
        private string $secretkey,
        #[Autowire('%oh_media_antispam.captcha.type%')]
        private string $type
    ) {
        if (OHMediaAntispamBundle::CAPTCHA_TYPE_HCAPTCHA === $type) {
            $this->url = 'https://hcaptcha.com/siteverify';
        } elseif (OHMediaAntispamBundle::CAPTCHA_TYPE_RECAPTCHA === $type) {
            $this->url = 'https://www.google.com/recaptcha/api/siteverify';
        }
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$value) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $mainRequest = $this->requestStack->getMainRequest();
        $remoteip = $mainRequest->getClientIp();

        $opts = ['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret' => $this->secretkey,
                'response' => $value,
                'remoteip' => $remoteip,
            ]),
        ]];

        $context = stream_context_create($opts);

        $result = @file_get_contents($this->url, false, $context);

        if (!$result) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $json = @json_decode($result);

        if (!$json->success) {
            $this->context->addViolation($constraint->message);
        }
    }
}
