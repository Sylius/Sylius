<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Attribute\Factory;

use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     *
     * @return ProductAttributeInterface
     */
    public function createTyped($type);
}
