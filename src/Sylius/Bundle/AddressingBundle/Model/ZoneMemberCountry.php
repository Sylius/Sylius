<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Model;

/**
 * Default country zone member model.
 *
 * @author Саша Стаменковић <umpirsky@gmail.com>
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
     *
     * @return ZoneMemberCountry
     */
    public function setCountry(CountryInterface $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->country->getName();
    }
}
