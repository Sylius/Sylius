<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TranslatableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Taxon implements TaxonInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var TaxonInterface
     */
    protected $root;

    /**
     * @var TaxonInterface
     */
    protected $parent;

    /**
     * @var Collection|TaxonInterface[]
     */
    protected $children;

    /**
     * @var int
     */
    protected $left;

    /**
     * @var int
     */
    protected $right;

    /**
     * @var int
     */
    protected $level;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->children = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string) $this->translate()->__toString();
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * @return TaxonInterface
     */
    public function getRoot()
    {
        return $this->root;
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
            $taxon->setParent($this);

            $this->children->add($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(TaxonInterface $taxon)
    {
        if ($this->hasChild($taxon)) {
            $taxon->setParent(null);

            $this->children->removeElement($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->translate()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->translate()->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->translate()->getSlug();
    }

    /**
     * {@inheritdoc}
     */
    public function setSlug($slug = null)
    {
        $this->translate()->setSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermalink()
    {
        $permalink = $this->translate()->getPermalink();

        if (null !== $permalink) {
            return $permalink;
        }

        if (null === $this->parent) {
            return $this->getSlug();
        }

        $this->setPermalink($permalink = $this->parent->getPermalink().'/'.$this->getSlug());

        return $permalink;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermalink($permalink)
    {
        $this->translate()->setPermalink($permalink);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);
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
    }
}
