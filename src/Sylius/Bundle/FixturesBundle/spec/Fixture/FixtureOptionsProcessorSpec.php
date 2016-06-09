<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FixturesBundle\Fixture;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureOptionsProcessor;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureOptionsProcessorInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @mixin FixtureOptionsProcessor
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureOptionsProcessorSpec extends ObjectBehavior
{
    function let(Processor $configurationProcessor)
    {
        $this->beConstructedWith($configurationProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Fixture\FixtureOptionsProcessor');
    }

    function it_implements_fixture_options_processor_interface()
    {
        $this->shouldImplement(FixtureOptionsProcessorInterface::class);
    }

    function it_processes_configuration(Processor $configurationProcessor, FixtureInterface $fixture)
    {
        $configurationProcessor->processConfiguration($fixture, [['option' => 'value']])->willReturn(['processed_option' => 'processed_value']);

        $this->process($fixture, [['option' => 'value']])->shouldReturn(['processed_option' => 'processed_value']);
    }
}
