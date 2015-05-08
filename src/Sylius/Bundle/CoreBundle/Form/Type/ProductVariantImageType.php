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

use Sylius\Bundle\ResourceBundle\Form\Type\ImageType;

class ProductVariantImageType extends ImageType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variant_image';
    }
}
