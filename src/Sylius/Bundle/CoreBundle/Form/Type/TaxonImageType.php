<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;


class TaxonImageType extends ImageType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_image_taxon';
    }
}