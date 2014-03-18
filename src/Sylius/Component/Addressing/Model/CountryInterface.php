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

/**
 * Country interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface CountryInterface
{
    public function getId();
    public function getName();
    public function setName($name);
    public function getIsoName();
    public function setIsoName($isoName);
    public function getProvinces();
    public function setProvinces(Collection $provinces);
    public function hasProvinces();
    public function addProvince(ProvinceInterface $province);
    public function removeProvince(ProvinceInterface $province);
    public function hasProvince(ProvinceInterface $province);
}
