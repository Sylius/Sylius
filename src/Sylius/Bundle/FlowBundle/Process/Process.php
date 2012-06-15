<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process;

use Sylius\Bundle\FlowBundle\Process\Builder\ProcessBuilderInterface;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;

/**
 * Base class for process.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Process implements ProcessInterface
{
    /**
     * Process scenario alias.
     *
     * @var string
     */
    protected $scenarioAlias;

    /**
     * Steps.
     *
     * @var array
     */
    protected $steps;

    /**
     * Ordered steps.
     *
     * @var array
     */
    protected $orderedSteps;

    protected $validator;

    /**
     * Display action route.
     *
     * @var string
     */
    protected $displayRoute;

    /**
     * Forward action route.
     *
     * @var string
     */
    protected $forwardRoute;

    protected $redirect;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->steps = array();
        $this->orderedSteps = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getScenarioAlias()
    {
        return $this->scenarioAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function setScenarioAlias($scenarioAlias)
    {
        $this->scenarioAlias = $scenarioAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * {@inheritdoc}
     */
    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderedSteps()
    {
        return $this->orderedSteps;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderedSteps(array $orderedSteps)
    {
        $this->orderedSteps = $orderedSteps;
    }

    /**
     * {@inheritdoc}
     */
    public function getStepByIndex($index)
    {
        if (!isset($this->orderedSteps[$index])) {
            throw new \InvalidArgumentException(spritnf('Step with index %d. does not exist', $index));
        }

        return $this->orderedSteps[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function getStepByName($name)
    {
        if (!$this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" does not exist', $name));
        }

        return $this->steps[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstStep()
    {
        return $this->getStepByIndex(0);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastStep()
    {
        return $this->getStepByIndex($this->countSteps() - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function countSteps()
    {
        return count($this->steps);
    }

    /**
     * {@inheritdoc}
     */
    public function addStep($name, StepInterface $step)
    {
        if ($this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" already exists', $name));
        }

        $this->steps[$name] = $this->orderedSteps[] = $step;
    }

    /**
     * {@inheritdoc}
     */
    public function removeStep($name)
    {
        if (!$this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" does not exist', $name));
        }

        $index = array_search($this->steps[$name], $this->orderedSteps);

        unset($this->steps[$name]);
        unset($this->orderedSteps[$index]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasStep($name)
    {
        return isset($this->steps[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayRoute()
    {
        return $this->displayRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayRoute($route)
    {
        $this->displayRoute = $route;
    }

    /**
     * {@inheritdoc}
     */
    public function getForwardRoute()
    {
        return $this->forwardRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function setForwardRoute($route)
    {
        $this->forwardRoute = $route;
    }

    public function getRedirect()
    {
        return $this->redirect;
    }

    public function setRedirect($redirect)
    {
        $this->redirect = $redirect;
    }

    public function getValidator()
    {
        return $this->validator;
    }

    public function setValidator(\Closure $validator)
    {
        $this->validator = $validator;
    }
}
