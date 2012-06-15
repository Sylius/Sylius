<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Process controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessController extends ContainerAware
{
    /**
     * Build and start process for given scenario.
     *
     * @param string $scenarioAlias
     *
     * @return Response
     */
    public function startAction($scenarioAlias)
    {
        $coordinator = $this->container->get('sylius_flow.coordinator');

        return $coordinator->start($scenarioAlias);
    }

    /**
     * Execute display action of given step.
     *
     * @param Request $request
     * @param string  $scenarioAlias
     * @param string  $stepName
     *
     * @return Response
     */
    public function displayAction(Request $request, $scenarioAlias, $stepName)
    {
        $this->container->get('sylius_flow.context')->setRequest($request);

        $coordinator = $this->container->get('sylius_flow.coordinator');

        return $coordinator->display($scenarioAlias, $stepName);
    }

    /**
     * Execute continue action of given step.
     *
     * @param Request $request
     * @param string  $scenarioAlias
     * @param string  $stepName
     *
     * @return Response
     */
    public function forwardAction(Request $request, $scenarioAlias, $stepName)
    {
        $this->container->get('sylius_flow.context')->setRequest($request);

        $coordinator = $this->container->get('sylius_flow.coordinator');

        return $coordinator->forward($scenarioAlias, $stepName);
    }
}
