<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tax category model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class TaxCategory implements TaxCategoryInterface
{
    protected $id;
    protected $name;
    protected $description;
    protected $rates;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getRates()
    {
        return $this->rates;
    }

    public function addRate(TaxRateInterface $rate)
    {
        if (!$this->hasRate($rate)) {
            $rate->setCategory($this);
            $this->rates->add($rate);
        }
    }

    public function removeRate(TaxRateInterface $rate)
    {
        if ($this->hasRate($rate)) {
            $rate->setCategory(null);
            $this->rates->removeElement($rate);
        }
    }

    public function hasRate(TaxRateInterface $rate)
    {
        return $this->rates->contains($rate);
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
