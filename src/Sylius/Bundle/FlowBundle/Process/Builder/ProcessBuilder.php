<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Builder;

use Sylius\Bundle\FlowBundle\Process\Process;
use Sylius\Bundle\FlowBundle\Process\Scenario\ProcessScenarioInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProcessBuilder implements ProcessBuilderInterface
{
    protected $steps;
    protected $container;

    protected $process;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function build(ProcessScenarioInterface $scenario)
    {
        $this->process = new Process();

        $scenario->build($this);

        return $this->process;
    }

    public function add($name, $step)
    {
        if (is_string($step)) {
            $step = $this->loadStep($step);
        }

        if (!$step instanceof StepInterface) {
            throw new \InvalidArgumentException('Step added via builder must implement "Sylius\Bundle\FlowBundle\Process\Step\StepInterface"');
        }

        if ($step instanceof ContainerAwareStep) {
            $step->setContainer($this->container);
        }

        $this->process->addStep($name, $step);

        return $this;
    }

    public function remove($name)
    {
        $this->process->removeStep($name);
    }

    public function has($name)
    {
        return $this->process->hasStep($name);
    }

    public function setDisplayRoute($route)
    {
        $this->process->setDisplayRoute($route);
    }

    public function setForwardRoute($route)
    {
        $this->process->setForwardRoute($route);
    }

    public function registerStep($alias, StepInterface $step)
    {
        if (isset($this->steps[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow step with alias "%s" is already registered', $alias));
        }

        $this->steps[$alias] = $step;
    }

    public function loadStep($alias)
    {
        if (!isset($this->steps[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow step with alias "%s" is not registered', $alias));
        }

        return $this->steps[$alias];
    }
}
