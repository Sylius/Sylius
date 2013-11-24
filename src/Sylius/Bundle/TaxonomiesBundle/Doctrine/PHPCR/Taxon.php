<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Doctrine\PHPCR;

use PHPCR\NodeInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\Taxon as BaseTaxon;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;

/**
 * Persistence specific taxon class for PHPCR-ODM.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class Taxon extends BaseTaxon
{
    /**
     * The universally unique ID of this taxon, for PHPCR references.
     *
     * @var string
     */
    protected $uuid;

    /**
     * The parent PHPCR-ODM document.
     *
     * @var Taxon|object
     */
    protected $parentDocument;

    /**
     * The PHPCR node to know the level.
     *
     * @var NodeInterface
     */
    protected $node;

    /**
     * A PHPCR document can have any kind of parent. setParent is already taken
     * and limited to TaxonInterface.
     *
     * @param object $parent a mapped phpcr-odm document
     *
     * @return $this
     */
    public function setParentDocument($parent)
    {
        $this->parentDocument = $parent;

        return $this;
    }

    /**
     * Get the parent document regardless of its type.
     *
     * @return object
     */
    public function getParentDocument()
    {
        return $this->parentDocument;
    }

    /**
     * Forward to setParentDocument. Note that setting the parent to null is
     * most likely not going to work when flushing.
     *
     * {@inheritDoc}
     */
    public function setParent(TaxonInterface $parent = null)
    {
        return $this->setParentDocument($parent);
    }
    /**
     * Ensure that the interface is respected if the parent is not a taxon.
     *
     * {@inheritDoc}
     */
    public function getParent()
    {
        if ($this->parent instanceof TaxonInterface) {
            return $this->parentDocument;
        }

        return null;
    }

    /**
     * Get the universally unique ID of this taxon.
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    public function getTaxonomy()
    {
        $document = $this->getParentDocument();
        while($document instanceof Taxon) {
            $document = $document->getParentDocument();
        }
        if (!$document instanceof TaxonomyInterface) {
            throw new \RuntimeException('Invalid tree for ' . $this->getId());
        }

        return $document;
    }

    public function getLevel()
    {
        $level = 0;
        $document = $this->getParentDocument();
        while($document instanceof Taxon) {
            $level++;
            $document = $document->getParentDocument();
        }

        return $level;
    }

    // TODO left and right are inherent in the phpcr-odm tree. do we need them?
}
