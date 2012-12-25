<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Shipping category model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ShippingCategory implements ShippingCategoryInterface
{
    protected $id;
    protected $name;
    protected $description;
    protected $methods;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->methods = new ArrayCollection();
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

    public function getMethods()
    {
        return $this->methods;
    }

    public function addMethod(ShippingMethodInterface $method)
    {
        if (!$this->hasMethod($method)) {
            $method->setCategory($this);
            $this->methods->add($method);
        }
    }

    public function removeMethod(ShippingMethodInterface $method)
    {
        if ($this->hasMethod($method)) {
            $method->setCategory(null);
            $this->methods->removeElement($method);
        }
    }

    public function hasMethod(ShippingMethodInterface $method)
    {
        return $this->methods->contains($method);
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
