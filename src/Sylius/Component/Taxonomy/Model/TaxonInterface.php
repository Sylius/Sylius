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

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SlugAwareInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface TaxonInterface extends CodeAwareInterface, TranslatableInterface, ResourceInterface, SlugAwareInterface
{
    /**
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * @return TaxonInterface|null
     */
    public function getRoot(): ?self;

    /**
     * @return TaxonInterface|null
     */
    public function getParent(): ?self;

    /**
     * @param TaxonInterface|null $taxon
     */
    public function setParent(?self $taxon): void;

    /**
     * @return Collection|TaxonInterface[]
     */
    public function getAncestors(): Collection;

    /**
     * @return Collection|TaxonInterface[]
     */
    public function getChildren(): Collection;

    /**
     * @param TaxonInterface $taxon
     *
     * @return bool
     */
    public function hasChild(self $taxon): bool;

    /**
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * @param TaxonInterface $taxon
     */
    public function addChild(self $taxon): void;

    /**
     * @param TaxonInterface $taxon
     */
    public function removeChild(self $taxon): void;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void;

    /**
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void;

    /**
     * @return int|null
     */
    public function getLeft(): ?int;

    /**
     * @param int|null $left
     */
    public function setLeft(?int $left): void;

    /**
     * @return int|null
     */
    public function getRight(): ?int;

    /**
     * @param int|null $right
     */
    public function setRight(?int $right): void;

    /**
     * @return int|null
     */
    public function getLevel(): ?int;

    /**
     * @param int|null $level
     */
    public function setLevel(?int $level): void;

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
     * @return TaxonTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
