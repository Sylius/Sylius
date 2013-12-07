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

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ActionResult;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use FOS\RestBundle\View\View;

/**
 * Default coordinator implementation.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Coordinator implements CoordinatorInterface
{
    /**
     * Router.
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * Process builder.
     *
     * @var ProcessBuilderInterface
     */
    protected $builder;

    /**
     * Process context.
     *
     * @var ProcessContextInterface
     */
    protected $context;

    /**
     * Registered scenarios.
     *
     * @var array
     */
    protected $scenarios;

    /**
     * Constructor.
     *
     * @param RouterInterface         $router
     * @param ProcessBuilderInterface $builder
     * @param ProcessContextInterface $context
     */
    public function __construct(RouterInterface $router, ProcessBuilderInterface $builder, ProcessContextInterface $context)
    {
        $this->router = $router;
        $this->builder = $builder;
        $this->context = $context;

        $this->scenarios = array();
    }

    /**
     * {@inheritdoc}
     */
    public function start($scenarioAlias, ParameterBag $queryParameters = null)
    {
        $process = $this->buildProcess($scenarioAlias);
        $step = $process->getFirstStep();

        $this->context->initialize($process, $step);
        $this->context->close();

        if (($validator = $this->context->isValid()) !== true) {
            return $validator->getResponse($step);
        }

        return $this->redirectToStepDisplayAction($process, $step, $queryParameters);
    }

    /**
     * {@inheritdoc}
     */
    public function display($scenarioAlias, $stepName, ParameterBag $queryParameters = null)
    {
        $process = $this->buildProcess($scenarioAlias);
        $step = $process->getStepByName($stepName);

        $this->context->initialize($process, $step);

        try {
            $this->context->rewindHistory();
        } catch (NotFoundHttpException $e) {
            //the step we are supposed to display was not found in the history.
            if (null === $this->context->getPreviousStep()) {
                //there is no previous step go to start
                return $this->start($scenarioAlias, $queryParameters);
            }

            //we will go back to previous step...
            $history = $this->context->getStepHistory();
            if (empty($history)) {
                //there is no history
                return $this->start($scenarioAlias);
            }
            $step = $process->getStepByName(end($history));

            $this->context->initialize($process, $step);

            return $this->redirectToStepDisplayAction($process, $step);
        }

        if (($validator = $this->context->isValid()) !== true) {
            return $validator->getResponse($step);
        }

        $result = $step->displayAction($this->context);

        return $this->processStepResult($process, $result);
    }

    /**
     * {@inheritdoc}
     */
    public function forward($scenarioAlias, $stepName)
    {
        $process = $this->buildProcess($scenarioAlias);
        $step = $process->getStepByName($stepName);

        $this->context->initialize($process, $step);
        $this->context->rewindHistory();

        if (($validator = $this->context->isValid()) !== true) {
            return $validator->getResponse($step);
        }

        $result = $step->forwardAction($this->context);

        return $this->processStepResult($process, $result);
    }

    public function processStepResult(ProcessInterface $process, $result)
    {
        if ($result instanceof Response || $result instanceof View) {
            return $result;
        }

        if ($result instanceof ActionResult) {
            // Handle explicit jump to step.
            if ($result->getNextStepName()) {
                $this->context->setNextStepByName($result->getNextStepName());

                return $this->redirectToStepDisplayAction($process, $this->context->getNextStep());
            }

            // Handle last step.
            if ($this->context->isLastStep()) {
                $this->context->close();

                $url = $this->router->generate($process->getRedirect());

                return new RedirectResponse($url);
            }

            // Handle default linear behaviour.
            return $this->redirectToStepDisplayAction($process, $this->context->getNextStep());
        }

        throw new \RuntimeException('Wrong action result, expected Response or ActionResult');
    }

    /**
     * {@inheritdoc}
     */
    public function registerScenario($alias, ProcessScenarioInterface $scenario)
    {
        if (isset($this->scenarios[$alias])) {
            throw new InvalidArgumentException(sprintf('Process scenario with alias "%s" is already registered', $alias));
        }

        $this->scenarios[$alias] = $scenario;
    }

    /**
     * {@inheritdoc}
     */
    public function loadScenario($alias)
    {
        if (!isset($this->scenarios[$alias])) {
            throw new InvalidArgumentException(sprintf('Process scenario with alias "%s" is not registered', $alias));
        }

        return $this->scenarios[$alias];
    }

    /**
     * Redirect to step display action.
     *
     * @param ProcessInterface $process
     * @param StepInterface    $step
     * @param ParameterBag     $queryParameters
     *
     * @return RedirectResponse
     */
    protected function redirectToStepDisplayAction(ProcessInterface $process, StepInterface $step, ParameterBag $queryParameters = null)
    {
        $this->context->addStepToHistory($step->getName());

        if (null !== $route = $process->getDisplayRoute()) {
            $url = $this->router->generate($route, array(
                'stepName' => $step->getName()
            ));

            return new RedirectResponse($url);
        }

        // Default parameters for display route
        $routeParameters = array(
                'scenarioAlias' => $process->getScenarioAlias(),
                'stepName'      => $step->getName(),
        );

        if (null !== $queryParameters) {
            $routeParameters = array_merge($queryParameters->all(), $routeParameters);
        }

        $url = $this->router->generate('sylius_flow_display', $routeParameters);

        return new RedirectResponse($url);
    }

    /**
     * Builds process for given scenario alias.
     *
     * @param string $scenarioAlias
     *
     * @return ProcessInterface
     */
    protected function buildProcess($scenarioAlias)
    {
        $processScenario = $this->loadScenario($scenarioAlias);

        $process = $this->builder->build($processScenario);
        $process->setScenarioAlias($scenarioAlias);

        return $process;
    }
}
