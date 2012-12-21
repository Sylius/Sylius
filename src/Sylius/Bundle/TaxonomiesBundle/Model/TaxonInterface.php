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
 * Interface for taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface TaxonInterface
{
    public function getId();
    public function getTaxonomy();
    public function setTaxonomy(TaxonomyInterface $taxonomy = null);
    public function isRoot();
    public function getParent();
    public function setParent(TaxonInterface $taxon = null);
    public function getName();
    public function setName($name);
    public function getSlug();
    public function setSlug($slug);
    public function getPermalink();
    public function setPermalink($permalink);
}
