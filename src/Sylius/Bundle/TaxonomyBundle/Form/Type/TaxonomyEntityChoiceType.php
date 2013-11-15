<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Form\Type;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonomyChoiceType;

/**
 * Taxonomy entity choice form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonomyEntityChoiceType extends TaxonomyChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
