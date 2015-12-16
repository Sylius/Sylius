<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\AttributeValue as BaseAttributeValue;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeValue extends BaseAttributeValue implements AttributeValueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return parent::getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product = null)
    {
        return parent::setSubject($product);
    }
}
