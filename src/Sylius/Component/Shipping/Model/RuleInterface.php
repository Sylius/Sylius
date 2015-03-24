<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Sylius\Component\Resource\Model\RuleInterface as BaseRuleInterface;

/**
 * Shipping method rule model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleInterface extends BaseRuleInterface
{
    const TYPE_WEIGHT = 'weight';

    /**
     * @return null|ShippingMethodInterface
     */
    public function getMethod();

    /**
     * @param null|ShippingMethodInterface $method
     */
    public function setMethod(ShippingMethodInterface $method = null);
}
