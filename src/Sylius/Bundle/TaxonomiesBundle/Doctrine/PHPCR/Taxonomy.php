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

use Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy as BaseTaxonomy;

/**
 * Persistence specific taxonomy class for PHPCR-ODM.
 *
 * @author David Buchmann <mail@davidbu.ch>
 */
class Taxonomy extends BaseTaxonomy
{
    /**
     * The parent document in the phpcr-odm tree.
     *
     * @var object
     */
    protected $parentDocument;

    /**
     * A PHPCR document can have any kind of parent. setParent is already taken
     * and limited to TaxonInterface.
     *
     * @param object $parent a mapped phpcr-odm document
     *
     * @return $this
     */
    public function setParentDocument($parentDocument)
    {
        $this->parentDocument = $parentDocument;

        return $this;
    }

    /**
     * Get the parent document of this taxonomy
     *
     * @return object
     */
    public function getParentDocument($parentDocument)
    {
        return $parentDocument;
    }
}
