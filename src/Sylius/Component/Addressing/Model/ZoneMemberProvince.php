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

/**
 * Default province zone member model.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ZoneMemberProvince extends ZoneMember
{
    /**
     * @var ProvinceInterface
     */
    protected $province;

    /**
     * @return ProvinceInterface
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param ProvinceInterface $province
     *
     * @return ZoneMemberProvince
     */
    public function setProvince(ProvinceInterface $province = null)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->province->getName();
    }
}
