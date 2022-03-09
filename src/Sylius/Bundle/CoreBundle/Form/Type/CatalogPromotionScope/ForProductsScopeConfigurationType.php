<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope;

use Sylius\Bundle\ProductBundle\Form\Type\ProductAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ForProductsScopeConfigurationType extends AbstractType
{
    public function __construct(private DataTransformerInterface $productsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('products', ProductAutocompleteChoiceType::class, [
            'label' => 'sylius.ui.products',
            'multiple' => true,
            'required' => false,
            'choice_name' => 'name',
            'choice_value' => 'code',
            'resource' => 'sylius.product',
            'constraints' => [
                new NotBlank(['groups' => 'sylius', 'message' => 'sylius.catalog_promotion_scope.for_products.not_empty'])
            ],
        ]);

        $builder->get('products')->addModelTransformer($this->productsToCodesTransformer);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_scope_product_configuration';
    }
}
