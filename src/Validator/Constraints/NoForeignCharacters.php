<?php

namespace OHMedia\AntispamBundle\Validator\Constraints;

use OHMedia\AntispamBundle\Validator\NoForeignCharactersValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NoForeignCharacters extends Constraint
{
    public string $message = 'This value contains foreign characters that are not allowed.';

    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }

    public function validatedBy(): string
    {
        return NoForeignCharactersValidator::class;
    }
}
