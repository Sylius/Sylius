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

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleInterface extends ResourceInterface
{
    const TYPE_ITEM_TOTAL = 'item_total';
    const TYPE_ITEM_COUNT = 'item_count';
    const TYPE_WEIGHT     = 'weight';

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @return null|ShippingMethodInterface
     */
    public function getMethod();

    /**
     * @param null|ShippingMethodInterface $method
     */
    public function setMethod(ShippingMethodInterface $method = null);
}
