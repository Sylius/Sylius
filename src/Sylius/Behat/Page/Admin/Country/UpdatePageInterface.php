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
     * @param $postcodeName
     *
     * @return bool
     */
    public function hasPostCode(string $postcodeName): bool;

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

    public function addPostCode(string $postcode, string $postcodeName): void;

    public function removePostCode(string $postcodeName): void;

    public function clickAddProvinceButton();

    public function clickAddPostCodeButton();

    /**
     * @param string $provinceName
     */
    public function nameProvince($provinceName);

    /**
     * @param $postcodeName
     */
    public function namePostCode($postcodeName);

    /**
     * @param string $provinceName
     */
    public function removeProvinceName($provinceName);

    /**
     * @param string $provinceCode
     */
    public function specifyProvinceCode($provinceCode);

    /**
     * @param $postcodeValue
     */
    public function specifyPostCodeValue($postcodeValue);

}
