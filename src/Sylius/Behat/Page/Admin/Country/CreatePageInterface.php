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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function addProvince(): void;

    public function specifyProvinceName(string $name): void;

    public function specifyProvinceCode(string $code): void;

    public function specifyProvinceAbbreviation(string $abbreviation): void;

    public function selectCountry(string $countryName): void;
}
