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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;

/**
 * Interface for process context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessContextInterface
{
    /**
     * Initialize context with process and current step.
     *
     * @param ProcessInterface $process
     * @param StepInterface    $currentStep
     */
    public function initialize(ProcessInterface $process, StepInterface $currentStep);

    /**
     * Get process.
     *
     * @return ProcessInterface
     */
    public function getProcess();

    /**
     * Get current step.
     *
     * @return StepInterface
     */
    public function getCurrentStep();

    /**
     * Get previous step.
     *
     * @return StepInterface
     */
    public function getPreviousStep();

    /**
     * Get next step.
     *
     * @return StepInterface
     */
    public function getNextStep();

    /**
     * Is current step the first step?
     *
     * @return Boolean
     */
    public function isFirstStep();

    /**
     * Is current step the last step?
     *
     * @return Boolean
     */
    public function isLastStep();

    /**
     * Override the default next step.
     */
    public function setNextStepByName($stepAlias);

    /**
     * Close context and clear all the data.
     */
    public function close();

    /**
     * Is current flow valid?
     *
     * @return Boolean
     */
    public function isValid();

    /**
     * Get storage.
     *
     * @return StorageInterface
     */
    public function getStorage();

    /**
     * Set storage.
     *
     * @param StorageInterface $storage
     */
    public function setStorage(StorageInterface $storage);

    /**
     * Get current request.
     *
     * @return Request
     */
    public function getRequest();

    /**
     * Set current request.
     *
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * Get progress in percents.
     *
     * @return integer
     */
    public function getProgress();

    /**
     * The array contains the history of all the step names.
     *
     * @return array()
     */
    public function getStepHistory();

    /**
     * Set a new history of step names.
     *
     * @param array $history
     */
    public function setStepHistory(array $history);

    /**
     * Add the given name to the history of step names.
     *
     * @param string $stepName
     */
    public function addStepToHistory($stepName);

    /**
     * Goes back from the end fo the history and deletes all step names until the current one is found.
     *
     * @throws NotFoundHttpException If the step name is not found in the history.
     */
    public function rewindHistory();
}
