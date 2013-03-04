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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Process controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessController extends ContainerAware
{
    /**
     * Build and start process for given scenario.
     * This action usually redirects to first step.
     *
     * @param string $scenarioAlias
     *
     * @return Response
     */
    public function startAction($scenarioAlias)
    {
        $coordinator = $this->container->get('sylius.process.coordinator');

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
        $this->container->get('sylius.process.context')->setRequest($request);

        $coordinator = $this->container->get('sylius.process.coordinator');

        try {
            return $coordinator->display($scenarioAlias, $stepName);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException('The step you are looking for is not found.');
        }
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
        $this->container->get('sylius.process.context')->setRequest($request);

        $coordinator = $this->container->get('sylius.process.coordinator');

        return $coordinator->forward($scenarioAlias, $stepName);
    }
}
