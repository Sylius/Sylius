<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Builder\Variable;

use Sylius\Component\Product\Builder\PrototypeBuilder as BasePrototypeBuilder;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\PrototypeInterface;
use Sylius\Component\Product\Model\Variable\VariableProductInterface;

/**
 * Prototype builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeBuilder extends BasePrototypeBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(PrototypeInterface $prototype, ProductInterface $product)
    {
        if (!$product instanceof VariableProductInterface) {
            throw new \InvalidArgumentException('The prototype builder from SyliusProductComponent supports only VariableProductInterface.');
        }

        foreach ($prototype->getOptions() as $option) {
            $product->addOption($option);
        }

        parent::build($prototype, $product);
    }
}
