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

/**
 * SubscriptionItemInterface
 *
 * Subscription item which is linked to a Variant
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionItemInterface
{
    /**
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * @param VariantInterface $variant
     * @return SubscriptionItemInterface
     */
    public function setVariant(VariantInterface $variant);

    /**
     * @return null|ProductInterface
     */
    public function getProduct();
}