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

use Sylius\Bundle\FlowBundle\Process\ProcessInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ContainerAwareStep;
use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Coordinator implements CoordinatorInterface
{
    protected $processes;
    protected $container;

    public function __construct(ContainerInterface $container, StorageInterface $storage)
    {
        $this->container = $container;
        $this->storage = $storage;

        $this->processes = array();
    }

    public function start($processAlias)
    {
        $process = $this->loadProcess($processAlias);
    }

    public function display($processAlias, $stepAlias)
    {
        $process = $this->loadProcess($processAlias);
    }

    public function forward($processAlias, $stepAlias)
    {
        $process = $this->loadProcess($processAlias);
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
}
