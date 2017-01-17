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
use Sylius\Bundle\ThemeBundle\Configuration\ConfigurationProcessorInterface;
use Sylius\Bundle\ThemeBundle\Configuration\SymfonyConfigurationProcessor;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SymfonyConfigurationProcessorSpec extends ObjectBehavior
{
    function let(ConfigurationInterface $configuration, Processor $processor)
    {
        $this->beConstructedWith($configuration, $processor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SymfonyConfigurationProcessor::class);
    }

    function it_implements_configuration_processor_interface()
    {
        $this->shouldImplement(ConfigurationProcessorInterface::class);
    }

    function it_proxies_configuration_processing_to_symfony_configuration_processor(
        ConfigurationInterface $configuration,
        Processor $processor
    ) {
        $processor
            ->processConfiguration($configuration, [['name' => 'example/theme']])
            ->willReturn(['name' => 'example/theme'])
        ;

        $this->process([['name' => 'example/theme']])->shouldReturn(['name' => 'example/theme']);
    }

    function it_does_not_catch_any_exception_thrown_by_symfony_configuration_processor(
        ConfigurationInterface $configuration,
        Processor $processor
    ) {
        $processor
            ->processConfiguration($configuration, [])
            ->willThrow(\Exception::class)
        ;

        $this->shouldThrow(\Exception::class)->duringProcess([]);
    }
}
