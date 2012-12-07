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
 * Model for taxonomies.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxonomy implements TaxonomyInterface
{
    protected $id;
    protected $root;
    protected $taxons;

    public function __construct()
    {
        $this->taxons = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setRoot(TaxonInterface $root)
    {
        $this->root = $root;

        return $this;
    }

    public function getTaxons()
    {
        return $this->taxons;
    }

    public function setTaxons(Collection $taxons)
    {
        $this->taxons = $taxons;

        return $this;
    }

    public function hasTaxon(TaxonInterface $taxon)
    {
        return $this->taxons->contains($taxon);
    }

    public function addTaxon(TaxonInterface $taxon)
    {
        if (!$this->hasTaxon($taxon)) {
            $taxon->setTaxonomy($this);
            $this->taxons->add($taxon);
        }

        return $this;
    }

    public function removeTaxon(TaxonInterface $taxon)
    {
        if ($this->hasTaxon($taxon)) {
            $taxon->setTaxonomy(null);
            $this->taxons->removeElement($taxon);
        }

        return $this;
    }

    public function getName()
    {
        return $this->root->getName();
    }

    public function setName($name)
    {
        $this->root->setName($name);

        return $this;
    }
}
