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
}
