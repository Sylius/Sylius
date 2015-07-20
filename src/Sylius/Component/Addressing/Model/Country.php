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
use Symfony\Component\Intl\Intl;

/**
 * Default country model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class Country implements CountryInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * Country name in ISO format.
     *
     * @var string
     */
    protected $isoName;

    /**
     * @var Collection|ProvinceInterface[]
     */
    protected $provinces;

    public function __construct()
    {
        $this->provinces = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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
    public function getName($locale = null)
    {
        return Intl::getRegionBundle()->getCountryName($this->isoName, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsoName()
    {
        return $this->isoName;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsoName($isoName)
    {
        $this->isoName = $isoName;

        return $this;
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
    public function setProvinces(Collection $provinces)
    {
        $this->provinces = $provinces;

        return $this;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasProvince(ProvinceInterface $province)
    {
        return $this->provinces->contains($province);
    }
}
