<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Configuration\Test;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestConfigurationProvider;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TestConfigurationProviderSpec extends ObjectBehavior
{
    function let(TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
        $this->beConstructedWith($testThemeConfigurationManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TestConfigurationProvider::class);
    }

    function it_implements_configuration_provider_interface()
    {
        $this->shouldImplement(ConfigurationProviderInterface::class);
    }

    function it_provides_configuration_based_on_test_configuration_manager(TestThemeConfigurationManagerInterface $testThemeConfigurationManager)
    {
        $testThemeConfigurationManager->findAll()->willReturn([
            ['name' => 'theme/name'],
        ]);

        $this->getConfigurations()->shouldReturn([
            ['name' => 'theme/name'],
        ]);
    }
}
