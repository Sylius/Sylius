<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup\Builder;

use Sylius\Bundle\FlowBundle\Setup\SetupInterface;
use Sylius\Bundle\FlowBundle\Setup\Step\ContainerAwareStep;
use Sylius\Bundle\FlowBundle\Setup\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SetupBuilder implements SetupBuilderInterface
{
    protected $setups;
    protected $steps;
    protected $storage;
    protected $container;
    protected $buildedSetup;

    public function __construct(ContainerInterface $container, StorageInterface $storage)
    {
        $this->container = $container;
        $this->storage = $storage;
    }

    public function build(SetupInterface $setup, array $options = array())
    {
        $this->buildedSetup = $setup;

        $setup->build($this, $options);

        return $setup;
    }

    public function addStep($step)
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
        $index = $this->buildedSetup->countSteps();
        $step->setIndex($index);
        $step->setSetup($this->buildedSetup);

        $this->buildedSetup->setStep($index, $step);

        if ($this->buildedSetup->hasStep($index - 1)) {
            $step->setPrevious($this->buildedSetup->getStep($index - 1));
            $this->buildedSetup->getStep($index - 1)->setNext($step);
        }

        return $this;
    }

    public function removeStep($step)
    {
    }

    public function registerSetup($alias, SetupInterface $setup)
    {
        if (isset($this->setups[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow setup with alias "%s" is already registered', $alias));
        }

        $this->setups[$alias] = $setup;
    }

    public function loadSetup($alias)
    {
        if (!isset($this->setups[$alias])) {
            throw new \InvalidArgumentException(sprintf('Flow setup with alias "%s" is not registered', $alias));
        }

        return $this->setups[$alias];
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
