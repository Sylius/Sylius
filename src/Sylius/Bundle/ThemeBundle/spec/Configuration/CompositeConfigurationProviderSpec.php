<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\CompositeConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeConfigurationProviderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeConfigurationProvider::class);
    }

    function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_returns_empty_array_if_no_configurations_are_loaded()
    {
        $this->getConfigurations()->shouldReturn([]);
    }

    function it_returns_sum_of_configurations_returned_by_nested_configuration_providers(
        ConfigurationProviderInterface $firstConfigurationProvider,
        ConfigurationProviderInterface $secondConfigurationProvider
    ) {
        $this->beConstructedWith([
            $firstConfigurationProvider,
            $secondConfigurationProvider,
        ]);

        $firstConfigurationProvider->getConfigurations()->willReturn([
            ['name' => 'first/theme'],
        ]);
        $secondConfigurationProvider->getConfigurations()->willReturn([
            ['name' => 'second/theme'],
            ['name' => 'third/theme'],
        ]);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'first/theme'],
            ['name' => 'second/theme'],
            ['name' => 'third/theme'],
        ]);
    }
}
