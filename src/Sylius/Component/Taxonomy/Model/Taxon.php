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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class Taxon implements TaxonInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $code;

    /**
     * @var TaxonInterface|null
     */
    protected $root;

    /**
     * @var TaxonInterface|null
     */
    protected $parent;

    /**
     * @var Collection|TaxonInterface[]
     */
    protected $children;

    /**
     * @var int|null
     */
    protected $left;

    /**
     * @var int|null
     */
    protected $right;

    /**
     * @var int|null
     */
    protected $level;

    /**
     * @var int|null
     */
    protected $position;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(): bool
    {
        return null === $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot(): ?TaxonInterface
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?TaxonInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(?TaxonInterface $parent): void
    {
        $this->parent = $parent;
        if (null !== $parent) {
            $parent->addChild($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAncestors(): Collection
    {
        $ancestors = [];

        for ($ancestor = $this->getParent(); null !== $ancestor; $ancestor = $ancestor->getParent()) {
            $ancestors[] = $ancestor;
        }

        return new ArrayCollection($ancestors);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(TaxonInterface $taxon): bool
    {
        return $this->children->contains($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(TaxonInterface $taxon): void
    {
        if (!$this->hasChild($taxon)) {
            $this->children->add($taxon);
        }

        if ($this !== $taxon->getParent()) {
            $taxon->setParent($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(TaxonInterface $taxon): void
    {
        if ($this->hasChild($taxon)) {
            $taxon->setParent(null);

            $this->children->removeElement($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft(): ?int
    {
        return $this->left;
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft(?int $left): void
    {
        $this->left = $left;
    }

    /**
     * {@inheritdoc}
     */
    public function getRight(): ?int
    {
        return $this->right;
    }

    /**
     * {@inheritdoc}
     */
    public function setRight(?int $right): void
    {
        $this->right = $right;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @param string|null $locale
     *
     * @return TaxonTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var TaxonTranslationInterface $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): TaxonTranslationInterface
    {
        return new TaxonTranslation();
    }
}
