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

/**
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
     * Country code ISO 3166-1 alpha-2.
     *
     * @var string
     */
    protected $code;

    /**
     * @var Collection|AdministrativeAreaInterface[]
     */
    protected $administrativeAreas;

    /**
     * @var bool
     */
    protected $enabled = true;

    public function __construct()
    {
        $this->administrativeAreas = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
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
    public function getAdministrativeAreas()
    {
        return $this->administrativeAreas;
    }

    /**
     * {@inheritdoc}
     */
    public function setAdministrativeAreas(Collection $administrativeAreas)
    {
        $this->administrativeAreas = $administrativeAreas;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdministrativeAreas()
    {
        return !$this->administrativeAreas->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addAdministrativeArea(AdministrativeAreaInterface $administrativeArea)
    {
        if (!$this->hasAdministrativeArea($administrativeArea)) {
            $this->administrativeAreas->add($administrativeArea);
            $administrativeArea->setCountry($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdministrativeArea(AdministrativeAreaInterface $administrativeArea)
    {
        if ($this->hasAdministrativeArea($administrativeArea)) {
            $this->administrativeAreas->removeElement($administrativeArea);
            $administrativeArea->setCountry(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdministrativeArea(AdministrativeAreaInterface $administrativeArea)
    {
        return $this->administrativeAreas->contains($administrativeArea);
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->enabled = false;
    }
}
