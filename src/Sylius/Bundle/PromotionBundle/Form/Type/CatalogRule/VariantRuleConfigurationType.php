<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Form\Type\CatalogRule;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class VariantRuleConfigurationType extends AbstractType
{
    private DataTransformerInterface $productVariantsToCodesTransformer;

    public function __construct(DataTransformerInterface $productVariantsToCodesTransformer)
    {
        $this->productVariantsToCodesTransformer = $productVariantsToCodesTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('variants', ResourceAutocompleteChoiceType::class, [
            'label' => 'sylius.ui.variants',
            'multiple' => true,
            'required' => false,
            'choice_name' => 'descriptor',
            'choice_value' => 'code',
            'resource' => 'sylius.product_variant',
        ]);

        $builder->get('variants')->addModelTransformer($this->productVariantsToCodesTransformer);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_rule_variant_configuration';
    }
}
