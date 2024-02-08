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

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class ShippingElement extends Element implements ShippingElementInterface
{
    public function getProductShippingCategory(): string
    {
        return $this->getElement('shipping_category')->getText();
    }

    public function getProductHeight(): float
    {
        return (float) $this->getElement('product_height')->getText();
    }

    public function getProductDepth(): float
    {
        return (float) $this->getElement('product_depth')->getText();
    }

    public function getProductWeight(): float
    {
        return (float) $this->getElement('product_weight')->getText();
    }

    public function getProductWidth(): float
    {
        return (float) $this->getElement('product_width')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'product_depth' => '#shipping tr:contains("Depth") td:nth-child(2)',
            'product_height' => '#shipping tr:contains("Height") td:nth-child(2)',
            'product_weight' => '#shipping tr:contains("Weight") td:nth-child(2)',
            'product_width' => '#shipping tr:contains("Width") td:nth-child(2)',
            'shipping_category' => '#shipping tr:contains("Shipping category") td:nth-child(2)',
        ]);
    }
}
