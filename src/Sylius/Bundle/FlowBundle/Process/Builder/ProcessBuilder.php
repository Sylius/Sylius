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
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidator;
use Sylius\Bundle\FlowBundle\Validator\ProcessValidatorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProcessBuilder implements ProcessBuilderInterface
{
    /**
     * Container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Registered steps.
     *
     * @var array
     */
    protected $steps;

    /**
     * Current process.
     *
     * @var ProcessInterface
     */
    protected $process;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ProcessScenarioInterface $scenario)
    {
        $this->process = new Process();

        $scenario->build($this);

        return $this->process;
    }

    /**
     * {@inheritdoc}
     */
    public function add($name, $step)
    {
        $this->assertHasProcess();

        if (is_string($step)) {
            $step = $this->loadStep($step);
        }

        if (!$step instanceof StepInterface) {
            throw new \InvalidArgumentException('Step added via builder must implement "Sylius\Bundle\FlowBundle\Process\Step\StepInterface"');
        }

        if ($step instanceof ContainerAwareInterface) {
            $step->setContainer($this->container);
        }

        $step->setName($name);

        $this->process->addStep($name, $step);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $this->assertHasProcess();

        $this->process->removeStep($name);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        $this->assertHasProcess();

        return $this->process->hasStep($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayRoute($route)
    {
        $this->assertHasProcess();

        $this->process->setDisplayRoute($route);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setForwardRoute($route)
    {
        $this->assertHasProcess();

        $this->process->setForwardRoute($route);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRedirect($redirect)
    {
        $this->assertHasProcess();

        $this->process->setRedirect($redirect);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($validator)
    {
        $this->assertHasProcess();

        if ($validator instanceof \Closure) {
            $validator = new ProcessValidator($validator, 'An error occurred.');
        }

        if (!$validator instanceof ProcessValidatorInterface) {
            throw new \InvalidArumentException();
        }

        $this->process->setValidator($validator);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function registerStep($alias, StepInterface $step)
    {
        if (isset($this->steps[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow step with alias "%s" is already registered', $alias));
        }

        $this->steps[$alias] = $step;
    }

    /**
     * {@inheritdoc}
     */
    public function loadStep($alias)
    {
        if (!isset($this->steps[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow step with alias "%s" is not registered', $alias));
        }

        return $this->steps[$alias];
    }

    /**
     * If process do not exists, throw exception.
     *
     * @throws \RuntimeException
     */
    protected function assertHasProcess()
    {
        if (!$this->process) {
            throw new \RuntimeException('Process is not set');
        }
    }
}
