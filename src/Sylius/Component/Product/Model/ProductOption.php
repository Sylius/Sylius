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
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class ProductOption implements ProductOptionInterface, \Stringable
{
    use TimestampableTrait;
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var int|null */
    protected $position;

    /** @var Collection<array-key, ProductOptionValueInterface> */
    protected $values;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        /** @var ArrayCollection<array-key, ProductOptionValueInterface> $this->values */
        $this->values = new ArrayCollection();

        $this->createdAt = new \DateTime();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getValues(): Collection
    {
        return $this->values;
    }

    public function addValue(ProductOptionValueInterface $optionValue): void
    {
        if (!$this->hasValue($optionValue)) {
            $optionValue->setOption($this);
            $this->values->add($optionValue);
        }
    }

    public function removeValue(ProductOptionValueInterface $optionValue): void
    {
        if ($this->hasValue($optionValue)) {
            $this->values->removeElement($optionValue);
            $optionValue->setOption(null);
        }
    }

    public function hasValue(ProductOptionValueInterface $optionValue): bool
    {
        return $this->values->contains($optionValue);
    }

    /**
     * @return ProductOptionTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var ProductOptionTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): ProductOptionTranslationInterface
    {
        return new ProductOptionTranslation();
    }
}
