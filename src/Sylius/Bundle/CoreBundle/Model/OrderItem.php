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

use Sylius\Bundle\CartBundle\Model\CartItem;
use Sylius\Bundle\OrderBundle\Model\OrderItemInterface as BaseOrderItemInterface;

/**
 * Order item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderItem extends CartItem implements OrderItemInterface
{
    /**
     * Product variant.
     *
     * @var VariantInterface
     */
    protected $variant;

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        return $this->variant->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(VariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(BaseOrderItemInterface $item)
    {
        return parent::equals($item) || ($item instanceof self
            && $item->getVariant() === $this->variant
            && $item->getUnitPrice() === $this->getUnitPrice()
        );
    }
}
