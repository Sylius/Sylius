<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Process\Context;

use Sylius\Bundle\FlowBundle\Process\Step\StepInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Process context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProcessContext implements ProcessContextInterface
{
    protected $current;
    protected $storage;
    protected $request;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function setCurrent(StepInterface $current)
    {
        $this->current = $current;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function complete()
    {
    }
}
