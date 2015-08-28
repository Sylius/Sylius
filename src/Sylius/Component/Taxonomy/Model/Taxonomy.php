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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\TranslationInterface;

/**
 * Model for taxonomies.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Taxonomy extends AbstractTranslatable implements TaxonomyInterface
{
    /**
     * Taxonomy id.
     *
     * @var mixed
     */
    protected $id;

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
        return $this->translate()->__toString();
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
        return $this->translate()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->translate()->setName($name);
        $this->root->setName($name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentLocale($currentLocale)
    {
        if (null !== $this->root) {
            $this->root->setCurrentLocale($currentLocale);
        }

        return parent::setCurrentLocale($currentLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setFallbackLocale($fallbackLocale)
    {
        if (null !== $this->root) {
            $this->root->setFallbackLocale($fallbackLocale);
        }

        return parent::setFallbackLocale($fallbackLocale);
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
        $root->setCurrentLocale($this->getCurrentLocale());
        $root->setFallbackLocale($this->getFallbackLocale());

        $this->root = $root;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons($taxonomy = null)
    {
        return $this->root->getChildren();
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons(Collection $collection)
    {
        foreach ($collection as $child) {
            $this->root->addChild($child);
        }
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

    /**
     * {@inheritdoc}
     */
    public static function getTranslationClass()
    {
        return get_class().'Translation';
    }

    public function addTranslation(TranslationInterface $translation)
    {
        parent::addTranslation($translation);

        if ($translation instanceof TaxonomyTranslation) {
            $this->root->setName($translation->getName());
        }
        return $this;
    }
}
