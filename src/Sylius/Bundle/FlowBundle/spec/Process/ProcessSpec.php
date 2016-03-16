<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface;

class ProcessSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Process');
    }

    function it_is_a_process()
    {
        $this->shouldImplement(ProcessInterface::class);
    }

    function its_forward_route_is_mutable()
    {
        $this->setForwardRoute('forward_route');
        $this->getForwardRoute()->shouldReturn('forward_route');
    }

    function its_forward_route_params_is_mutable()
    {
        $this->setForwardRouteParams(['name' => 'value']);
        $this->getForwardRouteParams()->shouldReturn(['name' => 'value']);
    }

    function its_display_route_is_mutable()
    {
        $this->setDisplayRoute('display_route');
        $this->getDisplayRoute()->shouldReturn('display_route');
    }

    function its_display_params_is_mutable()
    {
        $this->setDisplayRouteParams(['name' => 'value']);
        $this->getDisplayRouteParams()->shouldReturn(['name' => 'value']);
    }

    function its_redirect_params_is_mutable()
    {
        $this->setRedirectParams(['name' => 'value']);
        $this->getRedirectParams()->shouldReturn(['name' => 'value']);
    }

    function its_redirect_is_mutable()
    {
        $this->setRedirect('redirect');
        $this->getRedirect()->shouldReturn('redirect');
    }

    function its_scenario_is_mutable()
    {
        $this->setScenarioAlias('scenarioAlias');
        $this->getScenarioAlias()->shouldReturn('scenarioAlias');
    }

    function its_validator_is_mutable(ProcessValidatorInterface $validator)
    {
        $this->setValidator($validator);
        $this->getValidator()->shouldReturn($validator);
    }

    function its_step_is_mutable(StepInterface $step, StepInterface $secondStep)
    {
        $step->getName()->shouldBeCalled()->willReturn('name');
        $secondStep->getName()->shouldBeCalled()->willReturn('other_name');
        $this->setSteps(['name' => $step]);

        $this->addStep('other_name', $secondStep);
        $this->removeStep('name');

        $this->getSteps()->shouldReturn(['other_name' => $secondStep]);
        $this->getOrderedSteps()->shouldReturn([$secondStep]);
    }
}
