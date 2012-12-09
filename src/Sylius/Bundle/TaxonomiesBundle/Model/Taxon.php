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

/**
 * Model for taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxon implements TaxonInterface
{
    protected $id;
    protected $taxonomy;
    protected $parent;
    protected $name;
    protected $slug;
    protected $permalink;

    public function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(TaxonomyInterface $taxonomy = null)
    {
        $this->taxonomy = $taxonomy;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(TaxonInterface $parent)
    {
        $this->parent = $parent;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

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

    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }
}
