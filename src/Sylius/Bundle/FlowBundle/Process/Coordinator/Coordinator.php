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
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

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
    public function start($scenarioAlias)
    {
        $process = $this->buildProcess($scenarioAlias);
        $step = $process->getFirstStep();

        $this->context->initialize($process, $step);

        if (!$this->context->isValid()) {
            throw new NotFoundHttpException();
        }

        return $this->redirectToStepDisplayAction($process, $step);
    }

    /**
     * {@inheritdoc}
     */
    public function display($scenarioAlias, $stepName)
    {
        $process = $this->buildProcess($scenarioAlias);
        $step = $process->getStepByName($stepName);

        $this->context->initialize($process, $step);
        $this->context->rewindHistory();

        if (!$this->context->isValid()) {
            throw new NotFoundHttpException();
        }

        return $step->displayAction($this->context);
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

        if (!$this->context->isValid()) {
            throw new NotFoundHttpException();
        }

        $response = $step->forwardAction($this->context);

        if (!$this->context->isCompleted()) {
            return $response;
        }

        if ($this->context->isLastStep()) {
            $this->context->close();

            $url = $this->router->generate($process->getRedirect());

            return new RedirectResponse($url);
        }

        return $this->redirectToStepDisplayAction($process, $this->context->getNextStep());
    }

    /**
     * {@inheritdoc}
     */
    public function registerScenario($alias, ProcessScenarioInterface $scenario)
    {
        if (isset($this->scenarios[$alias])) {
            throw new \InvalidArgumentException(sprintf('Process scenario with alias "%s" is already registered', $alias));
        }

        $this->scenarios[$alias] = $scenario;
    }

    /**
     * {@inheritdoc}
     */
    public function loadScenario($alias)
    {
        if (!isset($this->scenarios[$alias])) {
            throw new \InvalidArgumentException(sprintf('Process scenario with alias "%s" is not registered', $alias));
        }

        return $this->scenarios[$alias];
    }

    /**
     * Redirect to step display action.
     *
     * @param ProcessInterface $process
     * @param StepInterface    $step
     *
     * @return RedirectResponse
     */
    protected function redirectToStepDisplayAction(ProcessInterface $process, StepInterface $step)
    {
        $history = $this->context->getStepHistory();
        array_push($history, $step->getName());
        $this->context->setStepHistory($history);

        if (null !== $route = $process->getDisplayRoute()) {
            $url = $this->router->generate($route, array(
                'stepName' => $step->getName()
            ));

            return new RedirectResponse($url);
        }

        $url = $this->router->generate('sylius_flow_display', array(
            'scenarioAlias' => $process->getScenarioAlias(),
            'stepName'      => $step->getName()
        ));

        return new RedirectResponse($url);
    }

    /**
     * Builds process for given scenario alias.
     *
     * @param string $scenarioAlias
     *
     * @return ProcessInterface
     */
    private function buildProcess($scenarioAlias)
    {
        $processScenario = $this->loadScenario($scenarioAlias);

        $process = $this->builder->build($processScenario);
        $process->setScenarioAlias($scenarioAlias);

        return $process;
    }
}
