<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Builder;

use Sylius\Bundle\ProductBundle\Builder\PrototypeBuilder as BasePrototypeBuilder;
use Sylius\Bundle\ProductBundle\Model\ProductInterface;
use Sylius\Bundle\ProductBundle\Model\PrototypeInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface;

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
            throw new \InvalidArgumentException('The prototype builder from SyliusVariableProductBundle supports only VariableProductInterface.');
        }

        foreach ($prototype->getOptions() as $option) {
            $product->addOption($option);
        }

        parent::build($prototype, $product);
    }
}
