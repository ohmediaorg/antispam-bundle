<?php

namespace OHMedia\AntispamBundle\Validator\Constraints;

use OHMedia\AntispamBundle\Validator\RecaptchaValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Recaptcha extends Constraint
{
    public $message = 'reCAPTCHA verification was unsuccessful.';

    public function validatedBy()
    {
        return RecaptchaValidator::class;
    }
}
