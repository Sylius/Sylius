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
    public function enable();

    public function disable();

    public function isCodeFieldDisabled(): bool;

    public function isThereProvince(string $provinceName): bool;

    public function isThereProvinceWithCode(string $provinceCode): bool;

    public function addProvince(string $name, string $code, string $abbreviation = null);

    public function removeProvince(string $provinceName);

    public function clickAddProvinceButton();

    public function nameProvince(string $provinceName);

    public function removeProvinceName(string $provinceName);

    public function specifyProvinceCode(string $provinceCode);
}
