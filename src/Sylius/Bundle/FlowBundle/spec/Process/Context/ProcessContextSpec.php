<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FlowBundle\Process\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProcessContextSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FlowBundle\Process\Context\ProcessContext');
    }

    function it_is_a_process_context()
    {
        $this->shouldImplement(ProcessContextInterface::class);
    }

    function it_initializes(
        $storage,
        ProcessInterface $process,
        StepInterface $currentStep,
        StepInterface $previousStep,
        StepInterface $nextStep
    ) {
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$previousStep, $currentStep, $nextStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(3);

        $this->initialize($process, $currentStep);

        $this->getNextStep()->shouldReturn($nextStep);
        $this->getCurrentStep()->shouldReturn($currentStep);
        $this->getPreviousStep()->shouldReturn($previousStep);
    }

    function it_is_valid(
        $storage,
        ProcessInterface $process,
        StepInterface $currentStep,
        StepInterface $previousStep,
        StepInterface $nextStep,
        ProcessValidatorInterface $processValidator
    ) {
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$previousStep, $currentStep, $nextStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(3);

        $this->initialize($process, $currentStep);

        $process->getValidator()->willReturn($processValidator);
        $processValidator->isValid($this)->willReturn(false);
        $this->isValid()->shouldReturn(false);

        $process->getValidator()->willReturn($processValidator);
        $processValidator->isValid($this)->willReturn(true);

        $process->getValidator()->willReturn(null);
        $currentStep->getName()->willReturn('current_step');
        $storage->get('history', [])->shouldBeCalled()->willReturn([]);
        $this->isValid()->shouldReturn(true);

        $process->getValidator()->willReturn(null);
        $currentStep->getName()->shouldBeCalled()->willReturn('current_step');
        $storage->get('history', [])->shouldBeCalled()->willReturn(['current_step']);
        $this->isValid()->shouldReturn(true);

        $process->getValidator()->willReturn(null);
        $currentStep->getName()->shouldBeCalled()->willReturn('other_step');
        $storage->get('history', [])->shouldBeCalled()->willReturn(['current_step']);
        $this->isValid()->shouldReturn(false);
    }

    function it_checks_if_it_is_the_first_step(
        $storage,
        ProcessInterface $process,
        StepInterface $firstStep,
        StepInterface $lastStep
    ) {
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$firstStep, $lastStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(2);

        $this->initialize($process, $firstStep);

        $this->isFirstStep()->shouldReturn(true);
    }

    function it_checks_if_it_is_the_last_step(
        $storage,
        ProcessInterface $process,
        StepInterface $firstStep,
        StepInterface $lastStep
    ) {
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$firstStep, $lastStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(2);

        $this->initialize($process, $lastStep);

        $this->isLastStep()->shouldReturn(true);
    }

    function it_closes_the_storage(
        $storage,
        ProcessInterface $process,
        StepInterface $firstStep,
        StepInterface $lastStep
    ) {
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$firstStep, $lastStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(2);

        $this->initialize($process, $lastStep);

        $storage->clear()->shouldBeCalled();

        $this->close();
    }

    function its_request_is_mutable(Request $request)
    {
        $this->setRequest($request);
        $this->getRequest()->shouldReturn($request);
    }

    function its_storage_is_mutable(StorageInterface $storage)
    {
        $this->setStorage($storage);
        $this->getStorage()->shouldReturn($storage);
    }

    function its_step_history_is_mutable($storage)
    {
        $storage->set('history', ['step_one'])->shouldBeCalled();
        $storage->get('history', [])->willReturn(['step_one']);
        $storage->set('history', ['step_one', 'step_two'])->shouldBeCalled();
        $storage->get('history', ['step_one'])->willReturn(['step_one', 'step_two']);

        $this->setStepHistory(['step_one']);
        $this->addStepToHistory('step_two');
    }

    function it_rewind_history(
        $storage,
        ProcessInterface $process,
        StepInterface $currentStep,
        StepInterface $previousStep,
        StepInterface $nextStep
    ) {
        $currentStep->getName()->willReturn('step_two');
        $process->getScenarioAlias()->shouldBeCalled();
        $storage->initialize(Argument::type('string'))->shouldBeCalled();
        $process->getOrderedSteps()->shouldBeCalled()->willReturn([$previousStep, $currentStep, $nextStep]);
        $process->countSteps()->shouldBeCalled()->willReturn(2);
        $this->initialize($process, $currentStep);

        $storage->get('history', [])->shouldBeCalled()->willreturn(['step_one', 'step_two', 'step_three']);
        $storage->set('history', ['step_one', 'step_two'])->shouldBeCalled();

        $this->rewindHistory();
    }
}
