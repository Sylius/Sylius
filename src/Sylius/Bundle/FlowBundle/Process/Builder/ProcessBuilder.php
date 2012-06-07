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

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProcessBuilder implements ProcessBuilderInterface
{
    protected $processs;
    protected $steps;
    protected $container;
    protected $buildedProcess;

    public function __construct(ContainerInterface $container, StorageInterface $storage)
    {
        $this->container = $container;
        $this->storage = $storage;
    }

    public function build(ProcessInterface $process, array $options = array())
    {
        $this->buildedProcess = $process;

        $process->build($this, $options);

        return $process;
    }

    public function add($name, $step)
    {
        if ($step instanceof StepInterface) {
            if ($step instanceof ContainerAwareStep) {
                $step->setContainer($this->container);
            }
        } else {
            if (is_string($step)) {
                $step = $this->loadStep($step);
            }
        }

        $step->setStorage($this->storage);
        $index = $this->buildedProcess->countSteps();
        $step->setIndex($index);
        $step->setProcess($this->buildedProcess);

        $this->buildedProcess->setStep($index, $step);

        if ($this->buildedProcess->hasStep($index - 1)) {
            $step->setPrevious($this->buildedProcess->getStep($index - 1));
            $this->buildedProcess->getStep($index - 1)->setNext($step);
        }

        return $this;
    }

    public function removeStep($step)
    {
    }

    public function registerProcess($alias, ProcessInterface $process)
    {
        if (isset($this->processs[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow process with alias "%s" is already registered', $alias));
        }

        $this->processs[$alias] = $process;
    }

    public function loadProcess($alias)
    {
        if (!isset($this->processs[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow process with alias "%s" is not registered', $alias));
        }

        return $this->processs[$alias];
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
