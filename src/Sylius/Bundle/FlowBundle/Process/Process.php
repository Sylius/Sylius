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
    protected $steps;
    protected $orderedSteps;

    protected $displayRoute;
    protected $forwardRoute;

    public function __construct()
    {
        $this->steps = array();
        $this->steps = array();
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    public function getOrderedSteps()
    {
        return $this->orderedSteps;
    }

    public function setOrderedSteps(array $orderedSteps)
    {
        $this->orderedSteps = $orderedSteps;
    }

    public function getStepByIndex($index)
    {
        if (!isset($this->orderedSteps[$index])) {
            throw new \InvalidArgumentException(spritnf('Step with index %d. does not exist', $index));
        }

        return $this->orderedSteps[$index];
    }

    public function getStepByName($name)
    {
        if (!$this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" does not exist', $name));
        }

        return $this->steps[$name];
    }

    public function getFirstStep()
    {
        return $this->getStepByIndex(0);
    }

    public function getLastStep()
    {
        return $this->getStepByIndex($this->countSteps() - 1);
    }

    public function countSteps()
    {
        return count($this->steps);
    }

    public function addStep($name, StepInterface $step)
    {
        if ($this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" already exists', $name));
        }

        $this->steps[$name] = $this->orderedSteps[] = $step;
    }

    public function removeStep($name)
    {
        if (!$this->hasStep($name)) {
            throw new \InvalidArgumentException(sprintf('Step with name "%s" does not exist', $name));
        }

        $index = array_search($this->steps[$name], $this->orderedSteps);

        unset($this->steps[$name]);
        unset($this->orderedSteps[$index]);
    }

    public function hasStep($name)
    {
        return isset($this->steps[$name]);
    }

    publci fun

    public function setDisplayRoute($route)
    {
        $this->process->setDisplayRoute($route);
    }

    public function setForwardRoute($route)
    {
        $this->process->setForwardRoute($route);
    }
}
