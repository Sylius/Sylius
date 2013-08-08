<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
