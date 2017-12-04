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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductVariantInterface extends
    TimestampableInterface,
    ResourceInterface,
    CodeAwareInterface,
    TranslatableInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string
     */
    public function getDescriptor(): string;

    /**
     * @return Collection|ProductOptionValueInterface[]
     */
    public function getOptionValues(): Collection;

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function addOptionValue(ProductOptionValueInterface $optionValue): void;

    /**
     * @param ProductOptionValueInterface $optionValue
     */
    public function removeOptionValue(ProductOptionValueInterface $optionValue): void;

    /**
     * @param ProductOptionValueInterface $optionValue
     *
     * @return bool
     */
    public function hasOptionValue(ProductOptionValueInterface $optionValue): bool;

    /**
     * @return ProductInterface|null
     */
    public function getProduct(): ?ProductInterface;

    /**
     * @param ProductInterface|null $product
     */
    public function setProduct(?ProductInterface $product): void;

    /**
     * @return int|null
     */
    public function getPosition(): ?int;

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void;

    /**
     * @param string|null $locale
     *
     * @return ProductVariantTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
