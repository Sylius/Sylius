<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface;

/**
 * Interface for setup object.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessInterface
{
    /**
     * Get scenario alias.
     *
     * @return string
     */
    public function getScenarioAlias();

    /**
     * Set scenario alias.
     *
     * @param string $scenarioAlias
     */
    public function setScenarioAlias($scenarioAlias);

    /**
     * Get a collection of steps.
     * Keys will be step names.
     *
     * @return array
     */
    public function getSteps();

    /**
     * Set steps.
     *
     * @param array $steps
     */
    public function setSteps(array $steps);

    /**
     * Get steps in correct order.
     *
     * @return array
     */
    public function getOrderedSteps();

    /**
     * Get first process step.
     *
     * @return StepInterface
     */
    public function getFirstStep();

    /**
     * Get last step.
     *
     * @return StepInterface
     */
    public function getLastStep();

    /**
     * Add step and name it.
     *
     * @param string        $name
     * @param StepInterface $step
     */
    public function addStep($name, StepInterface $step);

    /**
     * Remove step.
     *
     * @param string $name
     */
    public function removeStep($name);

    /**
     * Has step with given name?
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function hasStep($name);

    /**
     * Count all steps.
     *
     * @return integer
     */
    public function countSteps();

    /**
     * Get validator.
     *
     * @return \Closure
     */
    public function getValidator();

    /**
     * Set validator.
     *
     * @param \Closure $validator
     */
    public function setValidator(ProcessValidatorInterface $validator);

    /**
     * Get redirection after complete.
     *
     * @return string
     */
    public function getRedirect();

    /**
     * Set redirection after complete.
     *
     * @param string $redirect
     */
    public function setRedirect($redirect);

    /**
     * Get display route.
     *
     * @return string
     */
    public function getDisplayRoute();

    /**
     * Set display route.
     *
     * @param string $route
     */
    public function setDisplayRoute($route);

    /**
     * Get forward route.
     *
     * @return string
     */
    public function getForwardRoute();

    /**
     * Set forward route.
     *
     * @param string $route
     */
    public function setForwardRoute($route);

    /**
     * Get step by index/order
     *
     * @param string $index
     *
     * @return StepInterface
     */
    public function getStepByIndex($index);

    /**
     * Get step by name
     *
     * @param string $index
     *
     * @return StepInterface
     */
    public function getStepByName($index);
}
