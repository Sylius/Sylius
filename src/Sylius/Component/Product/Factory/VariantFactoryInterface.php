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

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface VariantFactoryInterface extends FactoryInterface
{
    /**
     * @param mixed $promotionId
     *
     * @return VariantInterface
     *
     * @throws \InvalidArgumentException
     */
    public function createForProduct($promotionId);
}
