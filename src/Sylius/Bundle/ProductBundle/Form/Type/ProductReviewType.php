<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ReviewBundle\Form\Type\ReviewType;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ProductReviewType extends ReviewType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_review';
    }
}
