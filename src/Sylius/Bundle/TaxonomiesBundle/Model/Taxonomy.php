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

    public function getId()
    {
    }

    public function getRoot()
    {
    }

    public function setRoot(TaxonInterface $taxon)
    {
    }

    public function getTaxons()
    {
    }

    public function setTaxons(Collection $taxons)
    {
    }

    public function hasTaxon(TaxonInterface $taxon)
    {
    }

    public function addTaxon(TaxonInterface $taxon)
    {
    }

    public function removeTaxon(TaxonInterface $taxon)
    {
    }

    public function getName()
    {
    }

    public function setName($name)
    {
    }
}
