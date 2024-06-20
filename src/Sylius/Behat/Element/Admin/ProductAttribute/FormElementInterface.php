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

namespace Sylius\Behat\Element\Admin\ProductAttribute;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function specifyCode(string $code): void;

    public function isCodeDisabled(): bool;

    public function nameIt(string $name, string $language): void;

    public function changeName(string $name, string $language): void;

    public function disableTranslatability(): void;

    public function isTypeDisabled(): bool;

    public function hasAttributeValue(string $value, string $localeCode): bool;

    public function addAttributeValue(string $value, string $localeCode): void;

    public function deleteAttributeValue(string $value, string $localeCode): void;

    public function changeAttributeValue(string $oldValue, string $newValue, string $localeCode): void;

    public function checkMultiple(): void;

    public function specifyMinValue(int $min): void;

    public function specifyMaxValue(int $max): void;
}
