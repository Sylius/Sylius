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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * Previous step.
     *
     * @var StepInterface
     */
    protected $previousStep;

    /**
     * Next step.
     *
     * @var StepInterface
     */
    protected $nextStep;

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

    /**
     * Progress in percents.
     *
     * @var integer
     */
    protected $progress;

    /**
     * Was the context initialized?
     *
     * @var Boolean
     */
    protected $intitialized;

    /**
     * Constructor.
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;

        $this->initialized = false;
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
            }
        }

        $this->initialized = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $this->assertInitialized();

        $validator = $this->process->getValidator();

        if (null !== $validator && !$validator->isValid()) {
            return $validator;
        }

        $history = $this->getStepHistory();

        return 0 === count($history) || in_array($this->currentStep->getName(), $history);
    }

    /**
     * {@inheritdoc}
     */
    public function getProcess()
    {
        $this->assertInitialized();

        return $this->process;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStep()
    {
        $this->assertInitialized();

        return $this->currentStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousStep()
    {
        $this->assertInitialized();

        return $this->previousStep;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextStep()
    {
        $this->assertInitialized();

        return $this->nextStep;
    }

    /**
     * {@inheritdoc}
     */
    public function isFirstStep()
    {
        $this->assertInitialized();

        return null === $this->previousStep;
    }

    /**
     * {@inheritdoc}
     */
    public function isLastStep()
    {
        $this->assertInitialized();

        return null === $this->nextStep;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->assertInitialized();

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
        $this->assertInitialized();

        return $this->progress;
    }

    /**
     * {@inheritdoc}
     */
    public function getStepHistory()
    {
        return $this->storage->get('history', array());
    }

    /**
     * {@inheritdoc}
     */
    public function setStepHistory(array $history)
    {
        $this->storage->set('history', $history);
    }

    /**
     * {@inheritdoc}
     */
    public function addStepToHistory($stepName)
    {
        $history = $this->getStepHistory();
        array_push($history, $stepName);
        $this->setStepHistory($history);
    }

    /**
     * {@inheritdoc}
     */
    public function rewindHistory()
    {
        $history = $this->getStepHistory();

        while ($top = end($history)) {
            if ($top != $this->currentStep->getName()) {
                array_pop($history);
            } else {
                break;
            }
        }

        if (0 === count($history)) {
            throw new NotFoundHttpException(sprintf('Step "%s" not found in step history.', $this->currentStep->getName()));
        }

        $this->setStepHistory($history);
    }

    /**
     * If context was not initialized, throw exception.
     *
     * @throws \RuntimeException
     */
    protected function assertInitialized()
    {
        if (!$this->initialized) {
            throw new \RuntimeException('Process context was not initialized');
        }
    }

    /**
     * Calculates progress based on current step index.
     *
     * @param integer $currentStepIndex
     */
    protected function calculateProgress($currentStepIndex)
    {
        $totalSteps = $this->process->countSteps();

        $this->progress = floor(($currentStepIndex + 1) / $totalSteps * 100);
    }

    /**
     * {@inheritdoc}
     */
    public function setNextStepByName($stepName)
    {
        $this->nextStep = $this->process->getStepByName($stepName);
    }
}
