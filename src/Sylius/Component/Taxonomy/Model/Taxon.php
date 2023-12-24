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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class Taxon implements TaxonInterface, \Stringable
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }
    use ToggleableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var TaxonInterface|null */
    protected $root;

    /** @var TaxonInterface|null */
    protected $parent;

    /** @var Collection<array-key, TaxonInterface> */
    protected $children;

    /** @var int|null */
    protected $left;

    /** @var int|null */
    protected $right;

    /** @var int|null */
    protected $level;

    /** @var int|null */
    protected $position;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        /** @var ArrayCollection<array-key, TaxonInterface> $this->children */
        $this->children = new ArrayCollection();
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

    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    public function getRoot(): ?TaxonInterface
    {
        return $this->root;
    }

    public function getParent(): ?TaxonInterface
    {
        return $this->parent;
    }

    public function setParent(?TaxonInterface $taxon): void
    {
        $this->parent = $taxon;
        if (null !== $taxon) {
            $taxon->addChild($this);
        }
    }

    /**
     * @return Collection<int, TaxonInterface>
     */
    public function getAncestors(): Collection
    {
        $ancestors = [];

        for ($ancestor = $this->getParent(); null !== $ancestor; $ancestor = $ancestor->getParent()) {
            $ancestors[] = $ancestor;
        }

        /** @var Collection<int, TaxonInterface> $collection */
        $collection = new ArrayCollection($ancestors);

        return $collection;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function hasChild(TaxonInterface $taxon): bool
    {
        return $this->children->contains($taxon);
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function addChild(TaxonInterface $taxon): void
    {
        if (!$this->hasChild($taxon)) {
            $this->children->add($taxon);
        }

        if ($this !== $taxon->getParent()) {
            $taxon->setParent($this);
        }
    }

    public function removeChild(TaxonInterface $taxon): void
    {
        if ($this->hasChild($taxon)) {
            $taxon->setParent(null);

            $this->children->removeElement($taxon);
        }
    }

    public function getEnabledChildren(): Collection
    {
        return $this->children->filter(
            function (TaxonInterface $childTaxon) {
                return $childTaxon->isEnabled();
            },
        );
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getFullname(string $pathDelimiter = ' / '): ?string
    {
        if ($this->isRoot()) {
            return $this->getName();
        }

        return sprintf(
            '%s%s%s',
            $this->getParent()->getFullname($pathDelimiter),
            $pathDelimiter,
            $this->getName(),
        );
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

    public function getLeft(): ?int
    {
        return $this->left;
    }

    public function setLeft(?int $left): void
    {
        $this->left = $left;
    }

    public function getRight(): ?int
    {
        return $this->right;
    }

    public function setRight(?int $right): void
    {
        $this->right = $right;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): void
    {
        $this->level = $level;
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
     * @return TaxonTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var TaxonTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }
}
