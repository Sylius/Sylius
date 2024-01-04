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

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface TaxonInterface extends CodeAwareInterface, TranslatableInterface, ResourceInterface, SlugAwareInterface, ToggleableInterface
{
    public function isRoot(): bool;

    public function getRoot(): ?self;

    public function getParent(): ?self;

    public function setParent(?self $taxon): void;

    /**
     * @return Collection<int, TaxonInterface>
     */
    public function getAncestors(): Collection;

    /**
     * @return Collection<array-key, TaxonInterface>
     */
    public function getChildren(): Collection;

    public function hasChild(self $taxon): bool;

    public function hasChildren(): bool;

    public function addChild(self $taxon): void;

    public function removeChild(self $taxon): void;

    /**
     * @return Collection<array-key, TaxonInterface>
     */
    public function getEnabledChildren(): Collection;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getFullname(string $pathDelimiter = ' / '): ?string;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getLeft(): ?int;

    public function setLeft(?int $left): void;

    public function getRight(): ?int;

    public function setRight(?int $right): void;

    public function getLevel(): ?int;

    public function setLevel(?int $level): void;

    public function getPosition(): ?int;

    public function setPosition(?int $position): void;

    /**
     * @return TaxonTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
