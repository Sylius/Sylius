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
    /**
     * Get taxonomy id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get root taxon.
     *
     * @return TaxonInterface
     */
    public function getRoot();

    /**
     * Set root taxon.
     *
     * @param TaxonInterface $root
     */
    public function setRoot(TaxonInterface $root);

    /**
     * Get all taxons except the root.
     *
     * @return TaxonInterface[]
     */
    public function getTaxons();

    /**
     * Has a taxon?
     *
     * @param TaxonInterface $taxon
     *
     * @return Boolean
     */
    public function hasTaxon(TaxonInterface $taxon);

    /**
     * Add taxon.
     *
     * @param TaxonInterface $taxon
     */
    public function addTaxon(TaxonInterface $taxon);

    /**
     * Remove taxon.
     *
     * @param TaxonInterface $taxon
     */
    public function removeTaxon(TaxonInterface $taxon);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set taxonomy name.
     *
     * @param string $name
     */
    public function setName($name);
}
