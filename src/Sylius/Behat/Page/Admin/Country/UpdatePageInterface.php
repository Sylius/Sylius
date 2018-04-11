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

    /**
     * @return bool
     */
    public function isCodeFieldDisabled();

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function isThereProvince($provinceName);

    /**
     * @param string $provinceCode
     *
     * @return bool
     */
    public function isThereProvinceWithCode($provinceCode);

    /**
     * @param $postCodeName
     *
     * @return bool
     */
    public function hasPostCode($postCodeName): bool;

    /**
     * @param string      $name
     * @param string      $code
     * @param string|null $abbreviation
     */
    public function addProvince($name, $code, $abbreviation = null);

    /**
     * @param string $provinceName
     */
    public function removeProvince($provinceName);

    public function addPostCode(string $postCode, string $postCodeName): void;

    public function removePostCode(string $postCodeName): void;

    public function clickAddProvinceButton();

    public function clickAddPostCodeButton();

    /**
     * @param string $provinceName
     */
    public function nameProvince($provinceName);

    /**
     * @param $postCodeName
     */
    public function namePostCode($postCodeName);

    /**
     * @param string $provinceName
     */
    public function removeProvinceName($provinceName);

    /**
     * @param string $provinceCode
     */
    public function specifyProvinceCode($provinceCode);

    /**
     * @param $postCodeValue
     */
    public function specifyPostCodeValue($postCodeValue);

}
