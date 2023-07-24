<?php

namespace OHMedia\AntispamBundle\Validator;

use OHMedia\AntispamBundle\DependencyInjection\Configuration;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class CaptchaValidator extends ConstraintValidator
{
    private $requestStack;
    private $secretkey;
    private $url;

    public function __construct(RequestStack $requestStack, string $secretkey, string $type)
    {
        $this->requestStack = $requestStack;
        $this->secretkey = $secretkey;

        if (Configuration::CAPTCHA_TYPE_HCAPTCHA === $type) {
            $this->url = 'https://hcaptcha.com/siteverify';
        } elseif (Configuration::CAPTCHA_TYPE_RECAPTCHA === $type) {
            $this->url = 'https://www.google.com/recaptcha/api/siteverify';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $masterRequest = $this->requestStack->getMasterRequest();
        $remoteip = $masterRequest->getClientIp();

        $opts = ['http' => [
            'method' => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret' => $this->secretkey,
                'response' => $value,
                'remoteip' => $remoteip
            ])
        ]];

        $context  = stream_context_create($opts);

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
