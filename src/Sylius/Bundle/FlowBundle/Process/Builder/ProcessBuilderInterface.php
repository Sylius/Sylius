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
    public function build(ProcessScenarioInterface $scenario);

    /**
     * Add a step with given name.
     *
     * @param string               $name
     * @param string|StepInterface $step Step alias or instance
     */
    public function add($name, $step);

    /**
     * Remove step with given name.
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Check whether or not process has given step.
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function has($name);

    /**
     * Set display route.
     *
     * @param string $route
     */
    public function setDisplayRoute($route);

    /**
     * Set forward route.
     *
     * @param string $route
     */
    public function setForwardRoute($route);

    /**
     * Set redirection route after completion.
     *
     * @param string $redirect
     */
    public function setRedirect($redirect);

    /**
     * Validation of process, if returns false, process is suspended.
     *
     * @param \Closure $validator
     */
    public function validate($validator);

    /**
     * Register new step.
     *
     * @param string        $alias
     * @param StepInterface $step
     */
    public function registerStep($alias, StepInterface $step);

    /**
     * Load step.
     *
     * @param string $alias
     *
     * @return StepInterface
     */
    public function loadStep($alias);
}
