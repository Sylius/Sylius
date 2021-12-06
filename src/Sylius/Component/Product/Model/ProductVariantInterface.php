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
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductVariantInterface extends
    TimestampableInterface,
    ResourceInterface,
    CodeAwareInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescriptor(): string;

    /**
     * @return Collection|ProductOptionValueInterface[]
     *
     * @psalm-return Collection<array-key, ProductOptionValueInterface>
     */
    public function getOptionValues(): Collection;

    public function addOptionValue(ProductOptionValueInterface $optionValue): void;

    public function removeOptionValue(ProductOptionValueInterface $optionValue): void;

    public function hasOptionValue(ProductOptionValueInterface $optionValue): bool;

    public function getProduct(): ?ProductInterface;

    public function setProduct(?ProductInterface $product): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    /**
     * @return ProductVariantTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
