<?php
/**
 * Created by PhpStorm.
 * User: piotrwalkow
 * Date: 13/11/2015
 * Time: 12:42
 */

namespace Sylius\Bundle\CoreBundle;

final class SyliusCoreEvents
{
    /**
     * should be thrown on login, change currency, adding products to carts, etc.
     */
    const SHOPPER_CONTEXT_CHANGE = 'sylius.shopper_context_change';

    const PRE_CART_CHANGE = 'sylius.context.pre_cart_change';

    const CART_CHANGE = 'sylius.context.cart_change';

    const POST_CART_CHANGE = 'sylius.context.post_cart_change';
}