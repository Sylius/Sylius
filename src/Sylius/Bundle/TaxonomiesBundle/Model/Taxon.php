<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Model for taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxon implements TaxonInterface
{
    /**
     * Taxon id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * The taxonomy of this taxon.
     *
     * @var TaxonomyInterface
     */
    protected $taxonomy;

    /**
     * Parent taxon.
     *
     * @var TaxonInterface
     */
    protected $parent;

    /**
     * Child taxons.
     *
     * @var Collection
     */
    protected $children;

    /**
     * Taxon name.
     *
     * @var string
     */
    protected $name;

    /**
     * Taxon slug.
     *
     * @var string
     */
    protected $slug;

    /**
     * Taxon permalink.
     *
     * @var string
     */
    protected $permalink;

    /**
     * Taxon description.
     *
     * @var string
     */
    protected $description;

    /**
     * Required by DoctrineExtensions.
     *
     * @var mixed
     */
    protected $left;

    /**
     * Required by DoctrineExtensions.
     *
     * @var mixed
     */
    protected $right;

    /**
     * Required by DoctrineExtensions.
     *
     * @var mixed
     */
    protected $level;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
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
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxonomy(TaxonomyInterface $taxonomy = null)
    {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(TaxonInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild(TaxonInterface $taxon)
    {
        return $this->children->contains($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(TaxonInterface $taxon)
    {
        if (!$this->hasChild($taxon)) {
            $taxon->setTaxonomy($this->taxonomy);
            $taxon->setParent($this);

            $this->children->add($taxon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(TaxonInterface $taxon)
    {
        if ($this->hasChild($taxon)) {
            $taxon->setTaxonomy(null);
            $taxon->setParent(null);

            $this->children->removeElement($taxon);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        if (null !== $this->permalink) {
            return $this->permalink;
        }

        if (null === $this->parent) {
            return $this->slug;
        }

        return $this->permalink = $this->parent->getPermalink().'/'.$this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * {@inheritdoc}
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * {@inheritdoc}
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }
}
