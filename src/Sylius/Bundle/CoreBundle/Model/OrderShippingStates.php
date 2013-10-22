<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

/**
 * Default order shipping states.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class OrderShippingStates
{
    const READY             = 'ready';
    const BACKORDER         = 'backorder';
    const DISPATCHED        = 'dispatched';
    const PARTIALLY_SHIPPED = 'partially_shipped';
    const SHIPPED           = 'shipped';
    const RETURNED          = 'returned';
}
