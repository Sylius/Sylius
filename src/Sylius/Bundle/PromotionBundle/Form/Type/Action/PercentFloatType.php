<?php

namespace Sylius\Bundle\PromotionBundle\Form\Type\Action;


use Sylius\Bundle\PromotionBundle\Form\DataTransformer\PercentFloatToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;

class PercentFloatType extends PercentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new PercentFloatToLocalizedStringTransformer($options['scale'], $options['type']));
    }
}
