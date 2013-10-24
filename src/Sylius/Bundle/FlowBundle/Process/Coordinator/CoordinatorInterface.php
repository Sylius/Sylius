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

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @param string       $scenarioAlias
     * @param ParameterBag $queryParameters
     *
     * @return RedirectResponse
     */
    public function start($scenarioAlias, ParameterBag $queryParameters = null);

    /**
     * Display step.
     *
     * @param string       $scenarioAlias
     * @param string       $stepName
     * @param ParameterBag $queryParameters
     *
     * @return Response
     */
    public function display($scenarioAlias, $stepName, ParameterBag $queryParameters = null);

    /**
     * Move forward.
     * If step was completed, redirect to next step, otherwise return response.
     *
     * @param string $scenarioAlias
     * @param string $stepName
     *
     * @return Response
     */
    public function forward($scenarioAlias, $stepName);

    /**
     * Register new process scenario.
     *
     * @param string                   $alias
     * @param ProcessScenarioInterface $scenario
     */
    public function registerScenario($alias, ProcessScenarioInterface $scenario);

    /**
     * Load process scenario with given alias.
     *
     * @param string $scenario
     *
     * @return ProcessScenarioInterface
     */
    public function loadScenario($scenario);
}
