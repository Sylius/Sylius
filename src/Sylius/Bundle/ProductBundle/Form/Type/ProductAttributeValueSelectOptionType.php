<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueSelectOptionType;

/**
 * @author Asier Marqués <asier@simettric.com>
 */
final class ProductAttributeValueSelectOptionType extends AttributeValueSelectOptionType
{

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_attribute_value_select_option';
    }

}
