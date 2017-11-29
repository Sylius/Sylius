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

namespace Sylius\Behat\Page\Admin\Country;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    /**
     * @return bool
     */
    public function isCodeFieldDisabled(): bool;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function isThereProvince(string $provinceName): bool;

    /**
     * @param string $provinceCode
     *
     * @return bool
     */
    public function isThereProvinceWithCode(string $provinceCode): bool;

    /**
     * @param string $name
     * @param string $code
     * @param string|null $abbreviation
     */
    public function addProvince(string $name, string $code, ?string $abbreviation = null): void;

    /**
     * @param string $provinceName
     */
    public function removeProvince(string $provinceName): void;

    public function clickAddProvinceButton(): void;

    /**
     * @param string $provinceName
     */
    public function nameProvince(string $provinceName): void;

    /**
     * @param string $provinceName
     */
    public function removeProvinceName(string $provinceName): void;

    /**
     * @param string $provinceCode
     */
    public function specifyProvinceCode(string $provinceCode): void;
}
