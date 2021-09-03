<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionRuleType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogPromotionRuleTypeExtension extends AbstractTypeExtension
{
    private DataTransformerInterface $productVariantsToCodesTransformer;

    public function __construct(DataTransformerInterface $productVariantsToCodesTransformer)
    {
        $this->productVariantsToCodesTransformer = $productVariantsToCodesTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('configuration', ResourceAutocompleteChoiceType::class, [
            'label' => 'sylius.ui.variants',
            'multiple' => true,
            'required' => false,
            'choice_name' => 'descriptor',
            'choice_value' => 'code',
            'resource' => 'sylius.product_variant',
        ]);

        $builder->get('configuration')->addModelTransformer($this->productVariantsToCodesTransformer);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CatalogPromotionRuleType::class];
    }
}
