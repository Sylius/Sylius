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

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

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
    function getScenarioAlias();

    /**
     * Set scenario alias.
     *
     * @param string $scenarioAlias
     */
    function setScenarioAlias($scenarioAlias);

    /**
     * Get a collection of steps.
     * Keys will be step names.
     *
     * @return array
     */
    function getSteps();

    /**
     * Set steps.
     *
     * @param array $steps
     */
    function setSteps(array $steps);

    /**
     * Get steps in correct order.
     *
     * @return array
     */
    function getOrderedSteps();

    /**
     * Get first process step.
     *
     * @return StepInterface
     */
    function getFirstStep();

    /**
     * Get last step.
     *
     * @return StepInterface
     */
    function getLastStep();

    /**
     * Add step and name it.
     *
     * @param string        $name
     * @param StepInterface $step
     */
    function addStep($name, StepInterface $step);

    /**
     * Remove step.
     *
     * @param string $name
     */
    function removeStep($name);

    /**
     * Has step with given name?
     *
     * @param string $name
     *
     * @return Boolean
     */
    function hasStep($name);

    /**
     * Count all steps.
     *
     * @return integer
     */
    function countSteps();

    /**
     * Get validator.
     *
     * @return \Closure
     */
    function getValidator();

    /**
     * Set validator.
     *
     * @param \Closure $validator
     */
    function setValidator(\Closure $validator);

    /**
     * Get redirection after complete.
     *
     * @return string
     */
    function getRedirect();

    /**
     * Set redirection after compelte.
     *
     * @param string $redirect
     */
    function setRedirect($redirect);

    /**
     * Get display route.
     *
     * @return string
     */
    function getDisplayRoute();

    /**
     * Set display route.
     *
     * @param string $route
     */
    function setDisplayRoute($route);

    /**
     * Get forward route.
     *
     * @param string $route
     */
    function getForwardRoute();

    /**
     * Set forward route.
     *
     * @param string $route
     */
    function setForwardRoute($route);

    /**
     * Get step by index/order
     *
     * @return StepInterface
     */
    function getStepByIndex($index);

    /**
     * Get step by name
     *
     * @return StepInterface
     */
    function getStepByName($index);
}
