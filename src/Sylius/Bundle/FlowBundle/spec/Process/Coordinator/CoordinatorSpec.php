<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process\Coordinator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Routing\RouterInterface;

class CoordinatorSpec extends ObjectBehavior
{
    public function let(RouterInterface $router, ProcessBuilderInterface $builder, ProcessContextInterface $context)
    {
        $this->beConstructedWith($router, $builder, $context);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Coordinator\Coordinator');
    }

    public function it_is_process_builder()
    {
        $this->shouldImplement('Sylius\Bundle\FlowBundle\Process\Coordinator\CoordinatorInterface');
    }
}
