<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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

class Product implements ProductInterface, \Stringable
{
    use TimestampableTrait, ToggleableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var Collection<array-key, AttributeValueInterface> */
    protected $attributes;

    /** @var Collection<array-key, ProductVariantInterface> */
    protected $variants;

    /** @var Collection<array-key, ProductOptionInterface> */
    protected $options;

    /** @var Collection<array-key, ProductAssociationInterface> */
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

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getDescriptor(): string
    {
        return trim(sprintf('%s (%s)', $this->getName(), $this->code));
    }

    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    public function getMetaKeywords(): ?string
    {
        return $this->getTranslation()->getMetaKeywords();
    }

    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->getTranslation()->setMetaKeywords($metaKeywords);
    }

    public function getMetaDescription(): ?string
    {
        return $this->getTranslation()->getMetaDescription();
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->getTranslation()->setMetaDescription($metaDescription);
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @return Collection<array-key, AttributeValueInterface>
     */
    public function getAttributesByLocale(
        string $localeCode,
        string $fallbackLocaleCode,
        ?string $baseLocaleCode = null,
    ): Collection {
        if (null === $baseLocaleCode || $baseLocaleCode === $fallbackLocaleCode) {
            $baseLocaleCode = $fallbackLocaleCode;
            $fallbackLocaleCode = null;
        }

        $attributes = $this->attributes->filter(
            function (AttributeValueInterface $attribute) use ($baseLocaleCode) {
                return $attribute->getLocaleCode() === $baseLocaleCode || null === $attribute->getLocaleCode();
            },
        );

        $attributesWithFallback = [];

        /** @var ProductAttributeValueInterface $attribute */
        foreach ($attributes as $attribute) {
            $attributesWithFallback[] = $this->getAttributeInDifferentLocale($attribute, $localeCode, $fallbackLocaleCode);
        }

        /** @var Collection<array-key, AttributeValueInterface> $collection */
        $collection = new ArrayCollection($attributesWithFallback);

        return $collection;
    }

    public function addAttribute(?AttributeValueInterface $attribute): void
    {
        /** @var ProductAttributeValueInterface $attribute */
        Assert::isInstanceOf(
            $attribute,
            ProductAttributeValueInterface::class,
            'Attribute objects added to a Product object have to implement ProductAttributeValueInterface',
        );

        if (!$this->hasAttribute($attribute)) {
            $attribute->setProduct($this);
            $this->attributes->add($attribute);
        }
    }

    public function removeAttribute(?AttributeValueInterface $attribute): void
    {
        /** @var ProductAttributeValueInterface $attribute */
        Assert::isInstanceOf(
            $attribute,
            ProductAttributeValueInterface::class,
            'Attribute objects removed from a Product object have to implement ProductAttributeValueInterface',
        );

        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->setProduct(null);
        }
    }

    public function hasAttribute(AttributeValueInterface $attribute): bool
    {
        return $this->attributes->contains($attribute);
    }

    public function hasAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): bool
    {
        return $this->getAttributeByCodeAndLocale($attributeCode, $localeCode) !== null;
    }

    public function getAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): ?AttributeValueInterface
    {
        $localeCode = $localeCode ?: $this->getTranslation()->getLocale();

        foreach ($this->attributes as $attribute) {
            if ($attribute->getAttribute()->getCode() === $attributeCode &&
                ($attribute->getLocaleCode() === $localeCode || null === $attribute->getLocaleCode())) {
                return $attribute;
            }
        }

        return null;
    }

    public function hasVariants(): bool
    {
        return !$this->getVariants()->isEmpty();
    }

    public function getVariants(): Collection
    {
        return $this->variants;
    }

    public function addVariant(ProductVariantInterface $variant): void
    {
        if (!$this->hasVariant($variant)) {
            $variant->setProduct($this);
            $this->variants->add($variant);
        }
    }

    public function removeVariant(ProductVariantInterface $variant): void
    {
        if ($this->hasVariant($variant)) {
            $variant->setProduct(null);
            $this->variants->removeElement($variant);
        }
    }

    public function hasVariant(ProductVariantInterface $variant): bool
    {
        return $this->variants->contains($variant);
    }

    public function getEnabledVariants(): Collection
    {
        return $this->variants->filter(
            function (ProductVariantInterface $productVariant) {
                return $productVariant->isEnabled();
            },
        );
    }

    public function hasOptions(): bool
    {
        return !$this->options->isEmpty();
    }

    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(ProductOptionInterface $option): void
    {
        if (!$this->hasOption($option)) {
            $this->options->add($option);
        }
    }

    public function removeOption(ProductOptionInterface $option): void
    {
        if ($this->hasOption($option)) {
            $this->options->removeElement($option);
        }
    }

    public function hasOption(ProductOptionInterface $option): bool
    {
        return $this->options->contains($option);
    }

    public function getAssociations(): Collection
    {
        return $this->associations;
    }

    public function addAssociation(ProductAssociationInterface $association): void
    {
        if (!$this->hasAssociation($association)) {
            $this->associations->add($association);
            $association->setOwner($this);
        }
    }

    public function removeAssociation(ProductAssociationInterface $association): void
    {
        if ($this->hasAssociation($association)) {
            $association->setOwner(null);
            $this->associations->removeElement($association);
        }
    }

    public function hasAssociation(ProductAssociationInterface $association): bool
    {
        return $this->associations->contains($association);
    }

    public function isSimple(): bool
    {
        return 1 === $this->variants->count() && !$this->hasOptions();
    }

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
        ?string $fallbackLocaleCode = null,
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
