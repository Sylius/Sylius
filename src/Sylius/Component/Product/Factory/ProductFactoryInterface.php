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

use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ProductFactoryInterface extends TranslatableFactoryInterface
{
    /**
     * @param mixed $archetypeCode
     *
     * @return ProductInterface
     */
    public function createFromArchetype($archetypeCode);
}
