<?php

namespace OHMedia\AntispamBundle\Validator;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;

class RecaptchaValidator extends ConstraintValidator
{
    const RECAPTCHA_URL = 'https://www.google.com/recaptcha/api/siteverify';

    private $requestStack;
    private $secretkey;

    public function __construct(RequestStack $requestStack, $secretkey)
    {
        $this->requestStack = $requestStack;
        $this->secretkey = $secretkey;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        $remoteip = $masterRequest->getClientIp();
        $response = $masterRequest->get('g-recaptcha-response');

        $opts = ['http' => [
            'method' => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret' => $this->secretkey,
                'response' => $response,
                'remoteip' => $remoteip
            ])
        ]];

        $context  = stream_context_create($opts);

        $result = @file_get_contents(self::RECAPTCHA_URL, false, $context);

        if (!$result) {
            return false;
        }

        $json = @json_decode($result);

        if (!$json->success) {
            $this->context->addViolation($constraint->message);
        }
    }
}
