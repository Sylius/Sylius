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
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariant implements ProductVariantInterface
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var Collection|ProductOptionValueInterface[]
     */
    protected $optionValues;

    /**
     * @var int
     */
    protected $position;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->optionValues = new ArrayCollection();

        $this->createdAt = new \DateTime();
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
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescriptor()
    {
        $name = empty($this->getName()) ? $this->getProduct()->getName() : $this->getName();

        return trim(sprintf('%s (%s)', $name, $this->code));
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ProductVariantTranslation();
    }
}
