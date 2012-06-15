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
     * Set ordered steps.
     *
     * @param array $steps
     */
    function setOrderedSteps(array $steps);

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
}
