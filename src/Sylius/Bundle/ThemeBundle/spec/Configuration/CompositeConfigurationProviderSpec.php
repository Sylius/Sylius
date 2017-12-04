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

namespace spec\Sylius\Bundle\ThemeBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;

final class CompositeConfigurationProviderSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([]);
    }

    function it_implements_configuration_provider_interface(): void
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_returns_empty_array_if_no_configurations_are_loaded(): void
    {
        $this->getConfigurations()->shouldReturn([]);
    }

    function it_returns_sum_of_configurations_returned_by_nested_configuration_providers(
        ConfigurationProviderInterface $firstConfigurationProvider,
        ConfigurationProviderInterface $secondConfigurationProvider
    ): void {
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
