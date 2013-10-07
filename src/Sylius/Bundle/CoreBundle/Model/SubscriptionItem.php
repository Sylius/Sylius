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

use Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItem as BaseSubscriptionItem;

/**
 * SubscriptionItem implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionItem extends BaseSubscriptionItem implements SubscriptionItemInterface
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
    public function getProduct()
    {
        return $this->variant->getProduct();
    }
}