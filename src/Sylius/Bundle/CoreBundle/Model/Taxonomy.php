<?php

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy as BaseTaxonomy;

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
