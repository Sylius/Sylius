<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process\Builder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Prophecy\Argument;

class ProcessBuilderSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilder');
    }

    function it_is_process_builder()
    {
        $this->shouldImplement(ProcessBuilderInterface::class);
    }

    function it_should_not_add_inactive_steps(
        StepInterface $step,
        ProcessScenarioInterface $scenario
    ) {
        $this->build($scenario);
        $step->isActive()->willReturn(false);
        $step->setName(Argument::any())->shouldNotBeCalled();

        $this->add('foobar', $step);
    }

    function it_should_add_active_steps(
        StepInterface $step,
        ProcessScenarioInterface $scenario
    ) {
        $step->getName()->willReturn(null);
        $this->build($scenario);
        $step->isActive()->willReturn(true);
        $step->setName('foobar')->shouldBeCalled();

        $this->add('foobar', $step);
    }
}
