<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Product\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Webmozart\Assert\Assert;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(): ?string
    {
        return $this->getTranslation()->getMetaKeywords();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->getTranslation()->setMetaKeywords($metaKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(): ?string
    {
        return $this->getTranslation()->getMetaDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription(?string $metaDescription): void
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesByLocale(string $localeCode, string $fallbackLocaleCode): Collection
    {
        $attributes = $this->attributes->filter(
            function (ProductAttributeValueInterface $attribute) use ($fallbackLocaleCode) {
                return $attribute->getLocaleCode() === $fallbackLocaleCode;
            }
        );

        $attributesWithFallback = [];
        foreach ($attributes as $attribute) {
            $attributesWithFallback[] = $this->getAttributeInDifferentLocale($attribute, $localeCode);
        }

        return new ArrayCollection($attributesWithFallback);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute(?AttributeValueInterface $attribute): void
    {
        Assert::isInstanceOf(
            $attribute,
            ProductAttributeValueInterface::class,
            'Attribute objects added to a Product object have to implement ProductAttributeValueInterface'
        );

        if (!$this->hasAttribute($attribute)) {
            $attribute->setProduct($this);
            $this->attributes->add($attribute);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(?AttributeValueInterface $attribute): void
    {
        Assert::isInstanceOf(
            $attribute,
            ProductAttributeValueInterface::class,
            'Attribute objects removed from a Product object have to implement ProductAttributeValueInterface'
        );

        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->setProduct(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(AttributeValueInterface $attribute): bool
    {
        return $this->attributes->contains($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): bool
    {
        $localeCode = $localeCode ?: $this->getTranslation()->getLocale();

        foreach ($this->attributes as $attribute) {
            if ($attribute->getAttribute()->getCode() === $attributeCode
                && $attribute->getLocaleCode() === $localeCode) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): ?AttributeValueInterface
    {
        if (null === $localeCode) {
            $localeCode = $this->getTranslation()->getLocale();
        }

        foreach ($this->attributes as $attribute) {
            if ($attribute->getAttribute()->getCode() === $attributeCode &&
                $attribute->getLocaleCode() === $localeCode) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariants(): bool
    {
        return !$this->getVariants()->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariants(): Collection
    {
        return $this->variants;
    }

    /**
     * {@inheritdoc}
     */
    public function addVariant(ProductVariantInterface $variant): void
    {
        if (!$this->hasVariant($variant)) {
            $variant->setProduct($this);
            $this->variants->add($variant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeVariant(ProductVariantInterface $variant): void
    {
        if ($this->hasVariant($variant)) {
            $variant->setProduct(null);
            $this->variants->removeElement($variant);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasVariant(ProductVariantInterface $variant): bool
    {
        return $this->variants->contains($variant);
    }

    /**
     * {@inheritdoc}
     */
    public function hasOptions(): bool
    {
        return !$this->options->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(ProductOptionInterface $option): void
    {
        if (!$this->hasOption($option)) {
            $this->options->add($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeOption(ProductOptionInterface $option): void
    {
        if ($this->hasOption($option)) {
            $this->options->removeElement($option);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(ProductOptionInterface $option): bool
    {
        return $this->options->contains($option);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociations(): Collection
    {
        return $this->associations;
    }

    /**
     * {@inheritdoc}
     */
    public function addAssociation(ProductAssociationInterface $association): void
    {
        if (!$this->hasAssociation($association)) {
            $this->associations->add($association);
            $association->setOwner($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssociation(ProductAssociationInterface $association): void
    {
        if ($this->hasAssociation($association)) {
            $association->setOwner(null);
            $this->associations->removeElement($association);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAssociation(ProductAssociationInterface $association): bool
    {
        return $this->associations->contains($association);
    }

    /**
     * {@inheritdoc}
     */
    public function isSimple(): bool
    {
        return 1 === $this->variants->count() && !$this->hasOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable(): bool
    {
        return !$this->isSimple();
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): ProductTranslationInterface
    {
        return new ProductTranslation();
    }

    /**
     * {@inheritdoc}
     */
    private function getAttributeInDifferentLocale(
        ProductAttributeValueInterface $attributeValue,
        ?string $localeCode
    ): AttributeValueInterface {
        if (!$this->hasAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode)) {
            return $attributeValue;
        }

        $attributeValueInDifferentLocale = $this->getAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode);
        if ('' === $attributeValueInDifferentLocale->getValue()
            || null === $attributeValueInDifferentLocale->getValue()) {
            return $attributeValue;
        }

        return $attributeValueInDifferentLocale;
    }
}
