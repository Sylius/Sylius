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

namespace Sylius\Behat\Element\Admin\Product;

interface AttributesFormElementInterface
{
    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function removeAttribute(string $attributeName, string $localeCode): void;

    public function getAttributeSelectText(string $attribute, string $localeCode): string;

    public function getNonTranslatableAttributeValue(string $attribute): string;

    public function hasAttribute(string $attributeName): bool;

    public function hasNonTranslatableAttributeWithValue(string $attributeName, string $value): bool;

    public function addNonTranslatableAttribute(string $attributeName, string $value): void;

    public function addAttribute(string $attributeName): void;

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void;

    public function getAttributeValue(string $attribute, string $localeCode): string;

    public function addSelectedAttributes(): void;

    public function getNumberOfAttributes(): int;

    public function hasAttributeError(string $attributeName, string $localeCode): bool;
}
