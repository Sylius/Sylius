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

final class CheckoutSubtotalElement extends Element implements CheckoutSubtotalElementInterface
{
    public function getProductQuantity(string $productName): int
    {
        return (int) ($this->getElement('item_quantity', ['%name%' => $productName])->getText());
    }

    protected function getDefinedElements(): array
    {
        return [
            'item_quantity' => '[data-test-item-subtotal-quantity="%name%"]',
        ];
    }
}
