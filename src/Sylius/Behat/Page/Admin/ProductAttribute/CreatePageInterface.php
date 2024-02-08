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

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name, string $language): void;

    public function disableTranslation(): void;

    public function isTypeDisabled(): bool;

    public function addAttributeValue(string $value, string $localeCode): void;

    public function specifyMinValue(int $min): void;

    public function specifyMaxValue(int $max): void;

    public function checkMultiple(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationErrors(): string;
}
