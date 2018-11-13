<?php

namespace Sylius\Bundle\PromotionBundle\Form\DataTransformer;


use Symfony\Component\Form\Extension\Core\DataTransformer\PercentToLocalizedStringTransformer;

class PercentFloatToLocalizedStringTransformer extends PercentToLocalizedStringTransformer
{
    public function reverseTransform($value)
    {
        return (float)parent::reverseTransform($value);
    }
}
