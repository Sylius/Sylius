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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductOptionInterface extends
    ResourceInterface,
    CodeAwareInterface,
    TimestampableInterface,
    TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    /**
     * @return Collection<array-key, ProductOptionValueInterface>
     */
    public function getValues(): Collection;

    public function addValue(ProductOptionValueInterface $optionValue): void;

    public function removeValue(ProductOptionValueInterface $optionValue): void;

    public function hasValue(ProductOptionValueInterface $optionValue): bool;

    /**
     * @return ProductOptionTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
