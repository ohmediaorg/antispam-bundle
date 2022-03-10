<?php

namespace OHMedia\AntispamBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Recaptcha extends Constraint
{
    public $message = 'reCAPTCHA verification was unsuccessful.';
    
    public function validatedBy()
    {
        return 'oh_media_recaptcha.validator.recaptcha';
    }
}
