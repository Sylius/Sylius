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

use Sylius\Behat\Element\Admin\Crud\FormElementInterface;

interface AttributesFormElementInterface extends FormElementInterface
{
    public function addAttribute(string $attributeName): void;

    public function addSelectedAttributes(): void;

    public function updateAttribute(string $attributeName, string $value, string $localeCode): void;

    public function removeAttribute(string $attributeName): void;

    public function hasAttribute(string $attributeName): bool;

    public function getNumberOfAttributes(): int;

    public function getAttributeValue(string $attribute, string $localeCode): string;

    public function getAttributeSelectText(string $attribute, string $localeCode): string;

    public function getValueNonTranslatableAttribute(string $attributeName): string;

    public function getAttributeValidationErrors(string $attributeName, string $localeCode): string;

    public function hasAttributeError(string $attributeName, string $localeCode): bool;
}
