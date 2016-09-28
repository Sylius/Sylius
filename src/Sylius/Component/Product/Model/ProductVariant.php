<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariant implements ProductVariantInterface
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
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var Collection|ProductOptionValueInterface[]
     */
    protected $optionValues;

    /**
     * @var \DateTime
     */
    protected $availableOn;

    /**
     * @var \DateTime
     */
    protected $availableUntil;

    public function __construct()
    {
        $this->optionValues = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->availableOn = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
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
    public function getOptionValues()
    {
        return $this->optionValues;
    }

    /**
     * {@inheritdoc}
     */
    public function addOptionValue(ProductOptionValueInterface $optionValue)
    {
        if (!$this->hasOptionValue($optionValue)) {
            $this->optionValues->add($optionValue);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeOptionValue(ProductOptionValueInterface $optionValue)
    {
        if ($this->hasOptionValue($optionValue)) {
            $this->optionValues->removeElement($optionValue);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOptionValue(ProductOptionValueInterface $optionValue)
    {
        return $this->optionValues->contains($optionValue);
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product = null)
    {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return (new DateRange($this->availableOn, $this->availableUntil))->isInRange();
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableOn()
    {
        return $this->availableOn;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableOn(\DateTime $availableOn = null)
    {
        $this->availableOn = $availableOn;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableUntil()
    {
        return $this->availableUntil;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableUntil(\DateTime $availableUntil = null)
    {
        $this->availableUntil = $availableUntil;
    }
}
