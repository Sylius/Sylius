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
    private $id;
    private $taxonomy;
    private $parent;
    private $name;
    private $slug;
    private $permalink;

    public function getId()
    {
    }

    public function getTaxonomy()
    {
    }

    public function setTaxonomy(TaxonomyInterface $taxonomy)
    {
    }

    public function getParent()
    {
    }

    public function setParent(TaxonInterface $parent)
    {
    }

    public function getName()
    {
    }

    public function setName($name)
    {
    }

    public function getSlug()
    {
    }

    public function setSlug($slug)
    {
    }

    public function getPermalink()
    {
    }

    public function setPermalink($permalink)
    {
    }
}
