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
use Sylius\Component\Attribute\Model\AttributeValueInterface as BaseAttributeValueInterface;
use Sylius\Component\Variation\Model\OptionInterface as BaseOptionInterface;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * Sylius catalog product model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Product implements ProductInterface
{
    /**
     * Product id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Product name.
     *
     * @var string
     */
    protected $name;

    /**
     * Permalink for the product.
     * Used in url to access it.
     *
     * @var string
     */
    protected $slug;

    /**
     * Product description.
     *
     * @var string
     */
    protected $description;

    /**
     * Available on.
     *
     * @var \DateTime
     */
    protected $availableOn;

    /**
     * Meta keywords.
     *
     * @var string
     */
    protected $metaKeywords;

    /**
     * Meta description.
     *
     * @var string
     */
    protected $metaDescription;

    /**
     * Attributes.
     *
     * @var Collection|BaseAttributeValueInterface[]
     */
    protected $attributes;

    /**
     * Product variants.
     *
     * @var Collection|BaseVariantInterface[]
     */
    protected $variants;

    /**
     * Product options.
     *
     * @var Collection|BaseOptionInterface[]
     */
    protected $options;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Deletion time.
     *
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->availableOn = new \DateTime();
        $this->attributes = new ArrayCollection();
        $this->variants = new ArrayCollection();
        $this->options = new ArrayCollection();
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return new \DateTime() >= $this->availableOn;
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
    public function setAvailableOn(\DateTime $availableOn)
    {
        $this->availableOn = $availableOn;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(Collection $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute(BaseAttributeValueInterface $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            $attribute->setProduct($this);
            $this->attributes->add($attribute);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(BaseAttributeValueInterface $attribute)
    {
        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->setProduct(null);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(BaseAttributeValueInterface $attribute)
    {
        return $this->attributes->contains($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributeByName($attributeName)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeByName($attributeName)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMasterVariant()
    {
        foreach ($this->variants as $variant) {
            if ($variant->isMaster()) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMasterVariant(BaseVariantInterface $masterVariant)
    {
        $masterVariant->setMaster(true);

        if (!$this->variants->contains($masterVariant)) {
            $masterVariant->setProduct($this);
            $this->variants->add($masterVariant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariants()
    {
        return !$this->getVariants()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants()
    {
        return $this->variants->filter(function (BaseVariantInterface $variant) {
            return !$variant->isDeleted() && !$variant->isMaster();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableVariants()
    {
        return $this->variants->filter(function (BaseVariantInterface $variant) {
            return !$variant->isDeleted() && !$variant->isMaster() && $variant->isAvailable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setVariants(Collection $variants)
    {
        $this->variants->clear();

        foreach ($variants as $variant) {
            $this->addVariant($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariant(BaseVariantInterface $variant)
    {
        if (!$this->hasVariant($variant)) {
            $variant->setProduct($this);
            $this->variants->add($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariant(BaseVariantInterface $variant)
    {
        if ($this->hasVariant($variant)) {
            $variant->setProduct(null);
            $this->variants->removeElement($variant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariant(BaseVariantInterface $variant)
    {
        return $this->variants->contains($variant);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOptions()
    {
        return !$this->options->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(Collection $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(BaseOptionInterface $option)
    {
        if (!$this->hasOption($option)) {
            $this->options->add($option);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(BaseOptionInterface $option)
    {
        if ($this->hasOption($option)) {
            $this->options->removeElement($option);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(BaseOptionInterface $option)
    {
        return $this->options->contains($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
