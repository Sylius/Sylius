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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ZoneMemberCountry extends ZoneMember
{
    /**
     * @var CountryInterface
     */
    protected $country;

    /**
     * @return CountryInterface
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param CountryInterface $country
     */
    public function setCountry(CountryInterface $country = null)
    {
        $this->country = $country;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->country->getName();
    }
}
