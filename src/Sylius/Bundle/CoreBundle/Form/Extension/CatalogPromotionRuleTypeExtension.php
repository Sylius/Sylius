<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionRuleType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogRule\VariantRuleConfigurationType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogPromotionRuleTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('configuration', VariantRuleConfigurationType::class, [
            'label' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CatalogPromotionRuleType::class];
    }
}
