<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Generator;

use Sylius\Bundle\ProductBundle\Generator\VariantGenerator;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Variation\Model\VariableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductVariantGenerator extends VariantGenerator
{
    /**
     * {@inheritdoc}
     *
     * @return ProductVariantInterface
     */
    protected function createVariant(VariableInterface $variable, array $optionMap, $permutation)
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = parent::createVariant($variable, $optionMap, $permutation);
        // temporary solution - it should be deeply considered how to determine generated variant price
        $productVariant->setPrice(rand(1000, 10000));

        return $productVariant;
    }

}
