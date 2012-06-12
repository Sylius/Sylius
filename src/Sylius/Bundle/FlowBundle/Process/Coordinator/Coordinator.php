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
use Symfony\Component\Routing\RouterInterface;

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
     * @param RouterInterface         $builder
     * @param ProcessContextInterface $context
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
    }

    /**
     * {@inheritdoc}
     */
    public function display($scenarioAlias, $stepName)
    {
        $process = $this->buildProcess($scenarioAlias);

        $step = $process->getStepByName($stepName);

        return $step->display($this->context);
    }

    /**
     * {@inheritdoc}
     */
    public function forward($scenarioAlias, $stepName)
    {
        $process = $this->buildProcess($scenarioAlias);

        $step = $process->getStepByName($stepName);

        return $step->forward($this->context);
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
     * Builds process for given scenario alias.
     *
     * @param string $scenarioAlias
     *
     * @return ProcessInterface
     */
    private function buildProcess($scenarioAlias)
    {
        $processScenario = $this->loadScenario($scenarioAlias);

        return $this->builder->build($processScenario);
    }

}
