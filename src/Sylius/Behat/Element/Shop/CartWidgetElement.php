<?php

declare(strict_types=1);

namespace Sylius\Behat\Element\Shop;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class CartWidgetElement extends Element implements CartWidgetElementInterface
{
    public function getCartTotalQuantity(): int
    {
        $cartTotal = $this->getElement('cart_button')->getText();
        preg_match('/.+,\s(\d+)\s(items|item)/', $cartTotal, $parts);

        return intval($parts[1] ?? 0);
    }

    protected function getDefinedElements(): array
    {
        return [
            'cart_button' => '[data-test-cart-button]',
        ];
    }
}
