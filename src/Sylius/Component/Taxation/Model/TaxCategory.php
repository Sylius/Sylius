<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxCategory implements TaxCategoryInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Collection|TaxRateInterface[]
     */
    protected $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * {@inheritdoc}
     */
    public function getRates()
    {
        return $this->rates;
    }

    /**
     * {@inheritdoc}
     */
    public function addRate(TaxRateInterface $rate)
    {
        if (!$this->hasRate($rate)) {
            $rate->setCategory($this);
            $this->rates->add($rate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeRate(TaxRateInterface $rate)
    {
        if ($this->hasRate($rate)) {
            $rate->setCategory(null);
            $this->rates->removeElement($rate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasRate(TaxRateInterface $rate)
    {
        return $this->rates->contains($rate);
    }
}
