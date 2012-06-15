<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Coordinator;

use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;

/**
 * This service coordinates the whole flow of process.
 * Executes steps and start flows.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CoordinatorInterface
{
    /**
     * Start scenario, should redirect to first step.
     *
     * @param string $scenarioAlias
     *
     * @return RedirectResponse
     */
    function start($scenarioAlias);

    /**
     * Display step.
     *
     * @param string $scenarioAlias
     * @param string $stepName
     *
     * @return Response
     */
    function display($scenarioAlias, $stepName);

    /**
     * Move forward.
     * If step was completed, redirect to next step, otherwise return response.
     *
     * @param string $scenarioAlias
     * @param string $stepName
     *
     * @return Response
     */
    function forward($scenarioAlias, $stepName);

    /**
     * Register new process scenario.
     *
     * @param string                   $alias
     * @param ProcessScenarioInterface $process
     */
    function registerScenario($alias, ProcessScenarioInterface $scenario);

    /**
     * Load process scenario with given alias.
     *
     * @param string $alias
     *
     * @return ProcessScenarioInterface
     */
    function loadScenario($scenario);
}
