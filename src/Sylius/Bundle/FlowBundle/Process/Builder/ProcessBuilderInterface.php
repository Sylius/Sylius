<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Builder;

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Process builder interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ProcessBuilderInterface
{
    /**
     * Build process by adding steps defined in scenario.
     *
     * @param ProcessScenarioInterface $scenario
     *
     * @return ProcessInterface
     */
    function build(ProcessScenarioInterface $scenario);

    /**
     * Add a step with given name.
     *
     * @param string               $name
     * @param string|StepInterface $step Step alias or instance
     */
    function add($name, $step);

    /**
     * Remove step with given name.
     *
     * @param string $name
     */
    function remove($name);

    /**
     * Check wether or not process has given step.
     *
     * @param string $name
     *
     * @return Boolean
     */
    function has($name);

    /**
     * Set display route.
     *
     * @param string $route
     */
    function setDisplayRoute($route);

    /**
     * Set forward route.
     *
     * @param string $route
     */
    function setForwardRoute($route);

    /**
     * Set redirection route after completion.
     *
     * @param string $redirect
     */
    function setRedirect($redirect);

    /**
     * Validation of process, if returns false, process is suspended.
     *
     * @param \Closure $validator
     */
    function validate(\Closure $validator);

    /**
     * Register new step.
     *
     * @param string        $alias
     * @param StepInterface $step
     */
    function registerStep($alias, StepInterface $step);

    /**
     * Load step.
     *
     * @param string $alias
     *
     * @return StepInterface
     */
    function loadStep($alias);
}
