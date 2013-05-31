<?php

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\TaxonomiesBundle\Entity\Taxonomy as BaseTaxonomy;

/**
 * Sylius core taxononomy entity.
 *
 */
class Taxonomy extends BaseTaxonomy
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setRoot(new Taxon());
    }

}
