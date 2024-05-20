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

namespace Sylius\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function isCodeFieldDisabled(): bool;

    public function addProvince(): void;

    public function specifyProvinceName(string $name): void;

    public function specifyProvinceCode(string $code): void;

    public function specifyProvinceAbbreviation(string $abbreviation): void;

    public function isThereProvince(string $provinceName): bool;

    public function isThereProvinceWithCode(string $provinceCode): bool;

    public function removeProvince(string $provinceName): void;

    /** @return array<array-key, string> */
    public function getFormValidationErrors(): array;
}
