<?php

namespace OHMedia\AntispamBundle\Validator;

use OHMedia\AntispamBundle\Validator\Constraints\NoForeignCharacters;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NoForeignCharactersValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoForeignCharacters) {
            throw new UnexpectedTypeException($constraint, NoForeignCharacters::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (preg_match("/\p{Cyrillic}+/u", $value) || preg_match("/\p{Han}+/u", $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
