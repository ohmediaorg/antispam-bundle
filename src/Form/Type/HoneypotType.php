<?php

namespace JstnThms\AntispamBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HoneypotType extends AbstractType
{
    public function getParent()
    {
        return TextType::class;
    }
}
