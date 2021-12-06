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
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface ProductInterface extends
    AttributeSubjectInterface,
    CodeAwareInterface,
    ResourceInterface,
    SlugAwareInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getMetaKeywords(): ?string;

    public function setMetaKeywords(?string $metaKeywords): void;

    public function getMetaDescription(): ?string;

    public function setMetaDescription(?string $metaDescription): void;

    public function hasVariants(): bool;

    /**
     * @return Collection|ProductVariantInterface[]
     *
     * @psalm-return Collection<array-key, ProductVariantInterface>
     */
    public function getVariants(): Collection;

    public function addVariant(ProductVariantInterface $variant): void;

    public function removeVariant(ProductVariantInterface $variant): void;

    public function hasVariant(ProductVariantInterface $variant): bool;

    /**
     * @return Collection|ProductVariantInterface[]
     *
     * @psalm-return Collection<array-key, ProductVariantInterface>
     */
    public function getEnabledVariants(): Collection;

    public function hasOptions(): bool;

    /**
     * @return Collection|ProductOptionInterface[]
     *
     * @psalm-return Collection<array-key, ProductOptionInterface>
     */
    public function getOptions(): Collection;

    public function addOption(ProductOptionInterface $option): void;

    public function removeOption(ProductOptionInterface $option): void;

    public function hasOption(ProductOptionInterface $option): bool;

    /**
     * @return Collection|ProductAssociationInterface[]
     *
     * @psalm-return Collection<array-key, ProductAssociationInterface>
     */
    public function getAssociations(): Collection;

    public function addAssociation(ProductAssociationInterface $association): void;

    public function removeAssociation(ProductAssociationInterface $association): void;

    public function isSimple(): bool;

    public function isConfigurable(): bool;

    /**
     * @return ProductTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
