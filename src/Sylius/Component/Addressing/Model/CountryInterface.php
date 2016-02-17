<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
interface CountryInterface extends ToggleableInterface, ResourceInterface, CodeAwareInterface
{
    /**
     * @return Collection|ProvinceInterface[]
     */
    public function getProvinces();

    /**
     * @param Collection $provinces
     */
    public function setProvinces(Collection $provinces);

    /**
     * @return bool
     */
    public function hasProvinces();

    /**
     * @param ProvinceInterface $province
     */
    public function addProvince(ProvinceInterface $province);

    /**
     * @param ProvinceInterface $province
     */
    public function removeProvince(ProvinceInterface $province);

    /**
     * @param ProvinceInterface $province
     *
     * @return bool
     */
    public function hasProvince(ProvinceInterface $province);
}
