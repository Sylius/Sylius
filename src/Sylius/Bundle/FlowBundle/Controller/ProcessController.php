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

use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Coordinator\CoordinatorInterface;
use Sylius\Bundle\FlowBundle\Process\Coordinator\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Process controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProcessController
{
    /**
     * @var CoordinatorInterface
     */
    protected $processCoordinator;

    /**
     * @var ProcessContextInterface
     */
    protected $processContext;

    public function __construct(CoordinatorInterface $processCoordinator, ProcessContextInterface $processContext)
    {
        $this->processCoordinator = $processCoordinator;
        $this->processContext = $processContext;
    }

    /**
     * Build and start process for given scenario.
     * This action usually redirects to first step.
     *
     * @param Request $request
     * @param string  $scenarioAlias
     *
     * @return Response
     */
    public function startAction(Request $request, $scenarioAlias)
    {
        return $this->processCoordinator->start($scenarioAlias, $request->query);
    }

    /**
     * Execute display action of given step.
     *
     * @param Request $request
     * @param string  $scenarioAlias
     * @param string  $stepName
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function displayAction(Request $request, $scenarioAlias, $stepName)
    {
        $this->processContext->setRequest($request);

        try {
            return $this->processCoordinator->display($scenarioAlias, $stepName, $request->query);
        } catch (InvalidArgumentException $e) {
            throw new NotFoundHttpException('The step you are looking for is not found.', $e);
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
        $this->processContext->setRequest($request);

        return $this->processCoordinator->forward($scenarioAlias, $stepName);
    }
}
