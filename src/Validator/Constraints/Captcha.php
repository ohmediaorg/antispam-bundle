<?php

namespace OHMedia\AntispamBundle\Validator\Constraints;

use OHMedia\AntispamBundle\Validator\CaptchaValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Captcha extends Constraint
{
    public $message = 'Captcha verification was unsuccessful.';

    public function validatedBy()
    {
        return CaptchaValidator::class;
    }
}
