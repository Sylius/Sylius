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

use Sylius\Bundle\CartBundle\Model\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolver returns cart item that needs to be added based on request.
 * Should be called only when adding/removing items.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemResolverInterface
{
    /**
     * Returns item to add.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    function resolveItemToAdd(Request $request);

    /**
     * Returns item to remove.
     *
     * @param Request $request
     *
     * @return ItemInterface
     */
    function resolveItemToRemove(Request $request);
}
