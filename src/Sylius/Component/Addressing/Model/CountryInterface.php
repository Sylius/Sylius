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

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface CountryInterface extends ToggleableInterface, ResourceInterface, CodeAwareInterface
{
    /**
     * @param string|null $locale
     *
     * @return string|null
     */
    public function getName(?string $locale = null): ?string;

    /**
     * @return Collection|ProvinceInterface[]
     */
    public function getProvinces(): Collection;

    /**
     * @return bool
     */
    public function hasProvinces(): bool;

    /**
     * @param ProvinceInterface $province
     */
    public function addProvince(ProvinceInterface $province): void;

    /**
     * @param ProvinceInterface $province
     */
    public function removeProvince(ProvinceInterface $province): void;

    /**
     * @param ProvinceInterface $province
     *
     * @return bool
     */
    public function hasProvince(ProvinceInterface $province): bool;

    /**
     * @return bool
     */
    public function hasPostcodes(): bool;

    /**
     * @return Collection|ProvinceInterface[]
     */
    public function getPostcodes(): Collection;

    /**
     * @param PostCodeInterface $postcode
     *
     * @return bool
     */
    public function hasPostcode(PostCodeInterface $postcode): bool;

    /**
     * @param $postcodes
     */
    public function setPostcodes($postcodes): void;

    /**
     * @param PostCodeInterface $postcode
     */
    public function addPostcode(PostCodeInterface $postcode): void;

    /**
     * @param PostCodeInterface $postcode
     */
    public function removePostcode(PostCodeInterface $postcode): void;
}
