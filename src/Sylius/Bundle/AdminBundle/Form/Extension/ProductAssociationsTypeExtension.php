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

namespace Sylius\Bundle\AdminBundle\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationsType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductAssociationsTypeExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => ProductAutocompleteChoiceType::class,
            'entry_options' => fn (ProductAssociationTypeInterface $productAssociationType) => [
                'label' => $productAssociationType->getName(),
                'multiple' => true,
            ],
        ]);
    }

    /**
     * @return iterable<class-string>
     */
    public static function getExtendedTypes(): iterable
    {
        return [ProductAssociationsType::class];
    }
}
