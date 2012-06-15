<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Context;

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Process context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessContext implements ProcessContextInterface
{
    /**
     * Process.
     *
     * @var ProcessInterface
     */
    protected $process;

    /**
     * Current step.
     *
     * @var StepInterface
     */
    protected $currentStep;

    protected $previousStep;
    protected $nextStep;

    /**
     * Is current step completed?
     *
     * @var Boolean
     */
    protected $completed;

    /**
     * Storage.
     *
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    protected $progress;

    /**
     * Constructor.
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;

        $this->completed = false;
        $this->progress = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ProcessInterface $process, StepInterface $currentStep)
    {
        $this->process = $process;
        $this->currentStep = $currentStep;

        $this->storage->initialize(md5($process->getScenarioAlias()));

        $steps = $process->getOrderedSteps();

        foreach ($steps as $index => $step) {
            if ($step === $currentStep) {
                $this->previousStep = isset($steps[$index-1]) ? $steps[$index-1] : null;
                $this->nextStep = isset($steps[$index+1]) ? $steps[$index+1] : null;

                $this->calculateProgress($index);

                if (null === $this->getState($step)) {
                    $this->setState($step, ProcessContextInterface::STEP_STATE_PENDING);
                }

                break;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $validator = $this->process->getValidator();

        if (null !== $validator && !$validator()) {
            return false;
        }

        foreach ($this->process->getOrderedSteps() as $step) {
            if ($this->currentStep === $step) {
                return true;
            }

            if (ProcessContextInterface::STEP_STATE_COMPLETED !== $this->getState($step)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
    {
        return $this->currentStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep()
    {
        return $this->previousStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStep()
    {
        return $this->nextStep;
    }

    public function isFirstStep()
    {
        return null === $this->previousStep;
    }

    public function isLastStep()
    {
        return null === $this->nextStep;
    }

    /**
     * {@inheritdoc}
     */
    public function complete()
    {
        $this->setState($this->currentStep, ProcessContextInterface::STEP_STATE_COMPLETED);
    }

    /**
     * {@inheritdoc}
     */
    public function isCompleted()
    {
        return ProcessContextInterface::STEP_STATE_COMPLETED === $this->getState($this->currentStep);
    }

    public function close()
    {
        $this->storage->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getProgress()
    {
        return $this->progress;
    }

    private function getState(StepInterface $step)
    {
        return $this->storage->get(sprintf('_state.%s', $step->getName()));
    }

    private function setState(StepInterface $step, $state)
    {
        $this->storage->set(sprintf('_state.%s', $step->getName()), $state);
    }

    protected function calculateProgress($currentStepIndex)
    {
        $totalSteps = $this->process->countSteps();

        $this->progress = floor(($currentStepIndex + 1) / $totalSteps * 100);
    }
}
