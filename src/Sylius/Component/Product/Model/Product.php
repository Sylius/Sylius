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
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Product implements ProductInterface
{
    use TimestampableTrait, ToggleableTrait;
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
     * @var \DateTime
     */
    protected $availableOn;

    /**
     * @var \DateTime
     */
    protected $availableUntil;

    /**
     * @var Collection|AttributeValueInterface[]
     */
    protected $attributes;

    /**
     * @var Collection|ProductVariantInterface[]
     */
    protected $variants;

    /**
     * @var Collection|ProductOptionInterface[]
     */
    protected $options;

    /**
     * @var Collection|ProductAssociationInterface[]
     */
    protected $associations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();
        $this->availableOn = new \DateTime();
        $this->attributes = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->variants = new ArrayCollection();
        $this->options = new ArrayCollection();
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
    public function getSlug()
    {
        return $this->getTranslation()->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug = null)
    {
        $this->getTranslation()->setSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->getTranslation()->getMetaKeywords();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->getTranslation()->setMetaKeywords($metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getTranslation()->getMetaDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
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
    public function addAttribute(AttributeValueInterface $attribute)
    {
        if (!$this->hasAttribute($attribute)) {
            $attribute->setSubject($this);
            $this->attributes->add($attribute);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(AttributeValueInterface $attribute)
    {
        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->setSubject(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(AttributeValueInterface $attribute)
    {
        return $this->attributes->contains($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributeByCode($attributeCode)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getAttribute()->getCode() === $attributeCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeByCode($attributeCode)
    {
        foreach ($this->attributes as $attribute) {
            if ($attributeCode === $attribute->getAttribute()->getCode()) {
                return $attribute;
            }
        }

        return null;
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
        return $this->variants;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableVariants()
    {
        return $this->variants->filter(function (ProductVariantInterface $variant) {
            return $variant->isAvailable();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function addVariant(ProductVariantInterface $variant)
    {
        if (!$this->hasVariant($variant)) {
            $variant->setProduct($this);
            $this->variants->add($variant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariant(ProductVariantInterface $variant)
    {
        if ($this->hasVariant($variant)) {
            $variant->setProduct(null);
            $this->variants->removeElement($variant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariant(ProductVariantInterface $variant)
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
    public function addOption(ProductOptionInterface $option)
    {
        if (!$this->hasOption($option)) {
            $this->options->add($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(ProductOptionInterface $option)
    {
        if ($this->hasOption($option)) {
            $this->options->removeElement($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(ProductOptionInterface $option)
    {
        return $this->options->contains($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociation(ProductAssociationInterface $association)
    {
        if (!$this->hasAssociation($association)) {
            $this->associations->add($association);
            $association->setOwner($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociation(ProductAssociationInterface $association)
    {
        if ($this->hasAssociation($association)) {
            $association->setOwner(null);
            $this->associations->removeElement($association);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation(ProductAssociationInterface $association)
    {
        return $this->associations->contains($association);
    }

    /**
     * {@inheritdoc}
     */
    public function isSimple()
    {
        return 1 === $this->variants->count() && !$this->hasOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return !$this->isSimple();
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation()
    {
        return new ProductTranslation();
    }
}
