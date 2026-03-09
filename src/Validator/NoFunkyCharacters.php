<?php

namespace OHMedia\AntispamBundle\Validator;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;

#[\Attribute]
class NoFunkyCharacters extends Compound
{
    protected ?string $label;

    public function __construct(?string $label = null)
    {
        parent::__construct([
            'label' => $label,
        ]);
    }

    public function getDefaultOption()
    {
        return 'label';
    }

    protected function getConstraints(array $options): array
    {
        $constraints = [];

        $label = $options['label'] ?? null;

        if ($label) {
            $constraints[] = new NoSuspiciousCharacters(
                restrictionLevelMessage: "$label contains characters that are not allowed by the current restriction-level.",
                invisibleMessage: "$label contains invisible characters which is not allowed.",
                mixedNumbersMessage: "$label is mixing numbers from different scripts which is not allowed.",
                hiddenOverlayMessage: "$label contains hidden overlay characters which is not allowed.",
            );

            $constraints[] = new NoForeignCharacters(
                message: "$label contains foreign characters that are not allowed.",
            );
        } else {
            $constraints[] = new NoSuspiciousCharacters();

            $constraints[] = new NoForeignCharacters();
        }

        return $constraints;
    }
}
