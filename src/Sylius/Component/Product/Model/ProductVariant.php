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
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class ProductVariant implements ProductVariantInterface
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

    /** @var ProductInterface|null */
    protected $product;

    /** @var Collection<array-key, ProductOptionValueInterface> */
    protected $optionValues;

    /** @var int|null */
    protected $position;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        /** @var ArrayCollection<array-key, ProductOptionValueInterface> $this->optionValues */
        $this->optionValues = new ArrayCollection();

        $this->createdAt = new \DateTime();
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
        $name = empty($this->getName()) ? $this->getProduct()->getName() : $this->getName();

        return trim(sprintf('%s (%s)', $name, $this->code));
    }

    public function getOptionValues(): Collection
    {
        return $this->optionValues;
    }

    public function addOptionValue(ProductOptionValueInterface $optionValue): void
    {
        if (!$this->hasOptionValue($optionValue)) {
            $this->optionValues->add($optionValue);
        }
    }

    public function removeOptionValue(ProductOptionValueInterface $optionValue): void
    {
        if ($this->hasOptionValue($optionValue)) {
            $this->optionValues->removeElement($optionValue);
        }
    }

    public function hasOptionValue(ProductOptionValueInterface $optionValue): bool
    {
        return $this->optionValues->contains($optionValue);
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(?ProductInterface $product): void
    {
        $this->product = $product;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return ProductVariantTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ProductVariantTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }
}
