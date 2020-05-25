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
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Webmozart\Assert\Assert;

class Product implements ProductInterface
{
    use TimestampableTrait, ToggleableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $code;

    /**
     * @var Collection|AttributeValueInterface[]
     *
     * @psalm-var Collection<array-key, AttributeValueInterface>
     */
    protected $attributes;

    /**
     * @var Collection|ProductVariantInterface[]
     *
     * @psalm-var Collection<array-key, ProductVariantInterface>
     */
    protected $variants;

    /**
     * @var Collection|ProductOptionInterface[]
     *
     * @psalm-var Collection<array-key, ProductOptionInterface>
     */
    protected $options;

    /**
     * @var Collection|ProductAssociationInterface[]
     *
     * @psalm-var Collection<array-key, ProductAssociationInterface>
     */
    protected $associations;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->createdAt = new \DateTime();

        /** @var ArrayCollection<array-key, AttributeValueInterface> $this->attributes */
        $this->attributes = new ArrayCollection();

        /** @var ArrayCollection<array-key, ProductAssociationInterface> $this->associations */
        $this->associations = new ArrayCollection();

        /** @var ArrayCollection<array-key, ProductVariantInterface> $this->variants */
        $this->variants = new ArrayCollection();

        /** @var ArrayCollection<array-key, ProductOptionInterface> $this->options */
        $this->options = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
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
    public function getAttributesByLocale(
        string $localeCode,
        string $fallbackLocaleCode,
        ?string $baseLocaleCode = null
    ): Collection {
        if (null === $baseLocaleCode || $baseLocaleCode === $fallbackLocaleCode) {
            $baseLocaleCode = $fallbackLocaleCode;
            $fallbackLocaleCode = null;
        }

        $attributes = $this->attributes->filter(
            function (ProductAttributeValueInterface $attribute) use ($baseLocaleCode) {
                return $attribute->getLocaleCode() === $baseLocaleCode;
            }
        );

        $attributesWithFallback = [];
        foreach ($attributes as $attribute) {
            $attributesWithFallback[] = $this->getAttributeInDifferentLocale($attribute, $localeCode, $fallbackLocaleCode);
        }

        return new ArrayCollection($attributesWithFallback);
    }

    /**
     * {@inheritdoc}
     */
    public function addAttribute(?AttributeValueInterface $attribute): void
    {
        /** @var ProductAttributeValueInterface $attribute */
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
        /** @var ProductAttributeValueInterface $attribute */
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

    public function getEnabledVariants(): Collection
    {
        return $this->variants->filter(
            function (ProductVariantInterface $productVariant) {
                return $productVariant->isEnabled();
            }
        );
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
     * @return ProductTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ProductTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ProductTranslationInterface
    {
        return new ProductTranslation();
    }

    protected function getAttributeInDifferentLocale(
        ProductAttributeValueInterface $attributeValue,
        string $localeCode,
        ?string $fallbackLocaleCode = null
    ): AttributeValueInterface {
        if (!$this->hasNotEmptyAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode)) {
            if (
                null !== $fallbackLocaleCode &&
                $this->hasNotEmptyAttributeByCodeAndLocale($attributeValue->getCode(), $fallbackLocaleCode)
            ) {
                return $this->getAttributeByCodeAndLocale($attributeValue->getCode(), $fallbackLocaleCode);
            }

            return $attributeValue;
        }

        return $this->getAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode);
    }

    protected function hasNotEmptyAttributeByCodeAndLocale(string $attributeCode, string $localeCode): bool
    {
        $attributeValue = $this->getAttributeByCodeAndLocale($attributeCode, $localeCode);
        if (null === $attributeValue) {
            return false;
        }

        $value = $attributeValue->getValue();
        if ('' === $value || null === $value || [] === $value) {
            return false;
        }

        return true;
    }
}
