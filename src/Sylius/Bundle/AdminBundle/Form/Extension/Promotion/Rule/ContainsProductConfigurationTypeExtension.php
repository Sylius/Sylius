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

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule;

use Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class ContainsProductConfigurationTypeExtension extends AbstractTypeExtension
{
    /** @param ProductRepositoryInterface<ProductInterface> $productRepository */
    public function __construct(private readonly ProductRepositoryInterface $productRepository)
    {
    }

    /** @param array<string, mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_code', ProductAutocompleteType::class, [
                'label' => 'sylius.form.promotion_action.add_product_configuration.product',
            ])
            ->get('product_code')->addModelTransformer(
                new ReversedTransformer(new ResourceToIdentifierTransformer($this->productRepository, 'code')),
            )
        ;
    }

    /** @return iterable<class-string> */
    public static function getExtendedTypes(): iterable
    {
        return [ContainsProductConfigurationType::class];
    }
}
