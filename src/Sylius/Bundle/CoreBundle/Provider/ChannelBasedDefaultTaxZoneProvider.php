<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelBasedDefaultTaxZoneProvider implements ZoneProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getZone(OrderInterface $order)
    {
        return $order->getChannel()->getDefaultTaxZone();
    }
}
