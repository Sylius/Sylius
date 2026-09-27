<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'domain/cart/shopping_cart.php',
        'domain/order/managing_orders.php',
        'domain/product/managing_price_history.php',
        'domain/product/managing_product_variants.php',
        'domain/product/managing_products.php',
        'domain/promotion/managing_promotion_coupons.php',
        'domain/promotion/managing_promotions.php',
        'domain/shipping/managing_shipping_methods.php',
    ])
;
