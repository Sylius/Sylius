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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Symfony\Component\Intl\Intl;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class Country implements CountryInterface
{
    use ToggleableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * Country code ISO 3166-1 alpha-2.
     *
     * @var string
     */
    protected $code;

    /**
     * @var Collection|ProvinceInterface[]
     */
    protected $provinces;

    public function __construct()
    {
        $this->provinces = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: $this->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName($locale = null)
    {
        return Intl::getRegionBundle()->getCountryName($this->code, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getProvinces()
    {
        return $this->provinces;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProvinces()
    {
        return !$this->provinces->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addProvince(ProvinceInterface $province)
    {
        if (!$this->hasProvince($province)) {
            $this->provinces->add($province);
            $province->setCountry($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeProvince(ProvinceInterface $province)
    {
        if ($this->hasProvince($province)) {
            $this->provinces->removeElement($province);
            $province->setCountry(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasProvince(ProvinceInterface $province)
    {
        return $this->provinces->contains($province);
    }
}
