<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderCheckoutTransitions
{
    const GRAPH = 'sylius_order_checkout';

    const TRANSITION_ADDRESS = 'address';
    const TRANSITION_COMPLETE = 'complete';
    const TRANSITION_READDRESS = 'readdress';
    const TRANSITION_RESELECT_PAYMENT = 'reselect_payment';
    const TRANSITION_RESELECT_SHIPPING = 'reselect_shipping';
    const TRANSITION_SELECT_PAYMENT = 'select_payment';
    const TRANSITION_SELECT_SHIPPING = 'select_shipping';

    private function __construct()
    {
    }
}
