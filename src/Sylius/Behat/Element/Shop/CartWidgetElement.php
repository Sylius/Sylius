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

namespace Sylius\Behat\Element\Shop;

use FriendsOfBehat\PageObjectExtension\Element\Element;

class CartWidgetElement extends Element implements CartWidgetElementInterface
{
    public function getCartTotalQuantity(): int
    {
        if (!$this->hasElement('cart_quantity')) {
            return 0;
        }

        $element = $this->getElement('cart_quantity');
        $attributeValue = $element->getAttribute('data-test-cart-quantity');

        return is_numeric($attributeValue) ? (int) $attributeValue : 0;
    }

    protected function getDefinedElements(): array
    {
        return [
            'cart_quantity' => '[data-test-cart-quantity]',
        ];
    }
}
