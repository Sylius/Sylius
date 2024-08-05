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
    public function hasAttributeInLocale(string $attribute, string $localeCode, string $value): bool
    {
        $attributeValue = $this->getElement('attribute_with_locale', ['%locale_code%' => $localeCode]);

        return str_contains($attributeValue->getText(), $value) && str_contains($attributeValue->getText(), $attribute);
    }

    public function hasNonTranslatableAttribute(string $attribute, float|string $value): bool
    {
        $attributeElement = $this->getElement('non_translatable-attribute');
        $hasName = $attributeElement->has('css', sprintf('[data-test-non-translatable-attribute-name="%s"]', $attribute));
        $hasValue = $attributeElement->has('css', sprintf('[data-test-non-translatable-attribute-value="%s"]', $value));

        return $hasName && $hasValue;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_name' => '[data-test-attribute-name="%name%"]',
            'attribute_value' => '[data-test-attribute-value]',
            'attribute_with_locale' => '[data-test-attribute-with-locale="%locale_code%"]',
            'attribute_without_locale' => '[data-test-attribute-without-locale]',
            'non_translatable-attribute' => '[data-test-non-translatable-attribute]',
        ]);
    }
}
