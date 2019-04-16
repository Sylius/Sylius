<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Element\Product\ShowPage;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class AttributesElement extends Element implements AttributesElementInterface
{
    public function getProductAttribute(string $attribute): string
    {
        return $this->getElement('attribute_value', ['%attribute%' => $attribute])->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_value' => '#attributes tr:contains("%attribute%") td:nth-child(2)',
        ]);
    }
}
