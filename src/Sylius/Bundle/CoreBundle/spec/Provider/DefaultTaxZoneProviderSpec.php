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
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultTaxZoneProviderSpec extends ObjectBehavior
{
    function let(SettingsInterface $settings)
    {
        $this->beConstructedWith($settings);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Provider\DefaultTaxZoneProvider');
    }

    function it_implements_default_tax_zone_provider_interface()
    {
        $this->shouldImplement(ZoneProviderInterface::class);
    }

    function it_provides_default_tax_zone_from_settings($settings, ZoneInterface $defaultTaxZone)
    {
        $settings->get('default_tax_zone')->willReturn($defaultTaxZone);

        $this->getZone()->shouldReturn($defaultTaxZone);
    }

    function it_returns_null_if_there_is_no_default_tax_zone_configured($settings)
    {
        $settings->get('default_tax_zone')->willReturn(null);

        $this->getZone()->shouldReturn(null);
    }
}
