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

namespace Sylius\Behat\Element\Admin\ExchangeRate;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function specifyField(string $field, string $value): void;

    public function isFieldDisabled(string $fieldName): bool;

    public function getRatio(): string;

    public function hasFormValidationError(string $expectedMessage): bool;
}
