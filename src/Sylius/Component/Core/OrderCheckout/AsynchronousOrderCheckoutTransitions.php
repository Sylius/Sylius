<?php

declare(strict_types=1);

namespace Sylius\Component\Core\OrderCheckout;

interface AsynchronousOrderCheckoutTransitions
{
    public const GRAPH = 'sylius_async_order_checkout';

    public const TRANSITION_ADDRESS = 'address';

    public const TRANSITION_COMPLETE = 'complete';

    public const TRANSITION_SELECT_PAYMENT = 'select_payment';

    public const TRANSITION_SELECT_SHIPPING = 'select_shipping';
}
