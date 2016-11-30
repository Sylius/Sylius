<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedDefaultTaxZoneProvider;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelBasedDefaultTaxZoneProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelBasedDefaultTaxZoneProvider::class);
    }

    function it_implements_default_tax_zone_provider_interface()
    {
        $this->shouldImplement(ZoneProviderInterface::class);
    }

    function it_provides_default_tax_zone_from_order_channel(
        ChannelInterface $channel,
        OrderInterface $order,
        ZoneInterface $defaultTaxZone
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getDefaultTaxZone()->willReturn($defaultTaxZone);

        $this->getZone($order)->shouldReturn($defaultTaxZone);
    }
}
