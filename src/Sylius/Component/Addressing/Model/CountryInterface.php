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
}
