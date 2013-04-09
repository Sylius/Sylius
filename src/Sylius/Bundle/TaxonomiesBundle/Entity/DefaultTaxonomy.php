<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Entity;


/**
 * Default entity for taxonomies.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultTaxonomy extends Taxonomy
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->setRoot(new DefaultTaxon());
    }
}
