<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\CatalogRule;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class ForVariantsRuleConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('variants', ResourceAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.variants',
                'multiple' => true,
                'required' => false,
                'choice_name' => 'descriptor',
                'choice_value' => 'code',
                'resource' => 'sylius.product_variant',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_rule_for_variants_configuration';
    }
}
