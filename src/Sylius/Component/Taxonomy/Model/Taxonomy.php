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
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * Model for taxonomies.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Taxonomy implements TaxonomyInterface
{
    /**
     * Taxonomy id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Taxonomy name.
     *
     * @var string
     */
    protected $name;

    /**
     * Root taxon.
     *
     * @var TaxonInterface
     */
    protected $root;

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getName();
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
        $this->root->setName($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoot(TaxonInterface $root)
    {
        $root->setTaxonomy($this);

        $this->root = $root;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons()
    {
        return $this->root->getChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxonsAsList()
    {
        $taxons = new ArrayCollection();

        foreach ($this->root->getChildren() as $child) {
            $taxons[] = $child;

            $this->getChildTaxons($child, $taxons);
        }

        return $taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxon(TaxonInterface $taxon)
    {
        return $this->root->hasChild($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxon(TaxonInterface $taxon)
    {
        $this->root->addChild($taxon);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxon(TaxonInterface $taxon)
    {
        $this->root->removeChild($taxon);

        return $this;
    }

    private function getChildTaxons(TaxonInterface $taxon, Collection $taxons)
    {
        foreach ($taxon->getChildren() as $child) {
            $taxons[] = $child;

            $this->getChildTaxons($child, $taxons);
        }
    }
}
