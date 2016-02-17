<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductVariantFactoryInterface extends FactoryInterface
{
    /**
     * @param mixed $id
     *
     * @return VariantInterface
     */
    public function createForProductWithId($id);
}
