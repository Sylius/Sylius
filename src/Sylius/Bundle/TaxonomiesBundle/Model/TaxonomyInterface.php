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
 * Taxonomy model interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxonomyInterface
{
    public function getId();
    public function getRoot();
    public function setRoot(TaxonInterface $root);
    public function getTaxons();
    public function hasTaxon(TaxonInterface $taxon);
    public function addTaxon(TaxonInterface $taxon);
    public function removeTaxon(TaxonInterface $taxon);
    public function getName();
    public function setName($name);
}
