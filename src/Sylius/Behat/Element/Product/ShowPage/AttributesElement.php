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

final class AttributesElement extends Element implements AttributesElementInterface
{
    public function hasAttributeInLocale(string $attribute, string $locale, string $value): bool
    {
        $values = $this->getDocument()->find('css', sprintf('#attributes .tab.segment[data-tab="%s"]', $locale));

        $attributeValue = $values->find('css', sprintf('tr:contains("%s") td:nth-child(2)', $attribute))->getText();

        return $attributeValue === $value;
    }

    public function hasNonTranslatableAttribute(string $attribute, string $value): bool
    {
        $attributeValue = $this->getDocument()->find('css', sprintf('.ui.segment[data-tab="non-translatable"] tr:contains("%s") td:nth-child(2)', $attribute))->getText();

        return str_contains($attributeValue, $value);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_value_in_locale' => '.attributes:contains("%locale%") ~ table tr:contains("%attribute%") td:nth-child(2)',
            'attributes_in_locale' => '#attributes :contains("%locale%")',
        ]);
    }
}
