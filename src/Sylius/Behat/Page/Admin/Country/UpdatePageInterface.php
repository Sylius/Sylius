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

    public function isThereProvince(string $provinceName): bool;

    public function isThereProvinceWithCode(string $provinceCode): bool;

    public function addProvince(string $name, string $code, ?string $abbreviation = null): void;

    public function removeProvince(string $provinceName): void;

    public function clickAddProvinceButton(): void;

    public function nameProvince(string $provinceName): void;

    public function removeProvinceName(string $provinceName): void;

    public function specifyProvinceCode(string $provinceCode): void;

    /** @return array<array-key, string> */
    public function getFormValidationErrors(): array;
}
