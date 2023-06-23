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

final class LowestPriceInformationElement extends Element implements LowestPriceInformationElementInterface
{
    public function isThereInformationAboutProductLowestPriceWithPrice(string $lowestPriceBeforeDiscount): bool
    {
        return $this->hasElement('lowest_price_information_element_with_price', [
            '%lowestPriceBeforeDiscount%' => $lowestPriceBeforeDiscount,
        ]);
    }

    public function isThereInformationAboutProductLowestPrice(): bool
    {
        return $this->hasElement('lowest_price_information_element') && $this->getElement('lowest_price_information_element')->isVisible();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'lowest_price_information_element' => '#lowest-price-before-discount:contains("The lowest price of this product from")',
            'lowest_price_information_element_with_price' => '#lowest-price-before-discount:contains("%lowestPriceBeforeDiscount%")',
        ]);
    }
}
