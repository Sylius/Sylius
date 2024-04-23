<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope;

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ForVariantsScopeConfigurationType extends AbstractType
{
    public function __construct(private DataTransformerInterface $productVariantsToCodesTransformer)
    {
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
        return 'sylius_catalog_promotion_scope_variant_configuration';
    }
}
