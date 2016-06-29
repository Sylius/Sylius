<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Attribute\Factory;

use Sylius\Product\Model\AttributeInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AttributeFactoryInterface extends FactoryInterface
{
    /**
     * @param string $type
     *
     * @return AttributeInterface
     */
    public function createTyped($type);
}
