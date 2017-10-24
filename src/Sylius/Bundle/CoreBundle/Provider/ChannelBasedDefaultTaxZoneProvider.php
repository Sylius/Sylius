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

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

final class ChannelBasedDefaultTaxZoneProvider implements ZoneProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getZone(OrderInterface $order): ?ZoneInterface
    {
        return $order->getChannel()->getDefaultTaxZone();
    }
}
