<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Resolver;

use Sylius\Bundle\CartBundle\Model\CartItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolver returns cart item that needs to be added based on request.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemResolverInterface
{
    /**
     * Returns item to add.
     * It takes empty and clean item object as first argument.
     *
     * @param CartItemInterface $item
     * @param Request           $request
     *
     * @return CartItemInterface
     */
    public function resolve(CartItemInterface $item, Request $request);
}
