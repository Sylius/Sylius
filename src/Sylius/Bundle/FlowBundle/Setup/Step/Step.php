<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Setup\Step;

use Sylius\Bundle\FlowBundle\Setup\SetupInterface;
use Sylius\Bundle\FlowBundle\Storage\StorageInterface;

abstract class Step implements StepInterface
{
    protected $setup;
    protected $storage;
    protected $index;
    protected $previous;
    protected $next;

    public function complete()
    {
        $this->getStorage()->set('sylius_flow.setup.' . $this->getSetup()->getAlias() . '.step.' . $this->getIndex() . '.completed', true);

        return $this;
    }

    public function isCompleted()
    {
        return $this->getStorage()->get('sylius_flow.setup.' . $this->getSetup()->getAlias() . '.step.' . $this->getIndex() . '.completed', false);
    }

    public function skip()
    {
        $this->getStorage()->set('sylius_flow.setup.' . $this->getSetup()->getAlias() . '.step.' . $this->getIndex() . '.skipped', true);

        return $this;
    }

    public function isSkipped()
    {
        return $this->getStorage()->get('sylius_flow.setup.' . $this->getSetup()->getAlias() . '.step.' . $this->getIndex() . '.skipped', false);
    }

    public function getSetup()
    {
        return $this->setup;
    }

    public function setSetup(SetupInterface $setup)
    {
        $this->setup = $setup;
    }

    public function getStorage()
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex($index)
    {
        $this->index = $index;
    }

    public function getPrevious()
    {
        if ($this->isSkipped()) {
            return $this->previous->getPrevious();
        }

        return $this->previous;
    }

    public function setPrevious(StepInterface $step)
    {
        $this->previous = $step;
    }

    public function hasPrevious()
    {
        if ($this->isSkipped()) {
            return $this->previous->hasPrevious();
        }

        return null !== $this->previous;
    }

    public function getNext()
    {
        if ($this->isSkipped()) {
            return $this->next->getNext();
        }

        return $this->next;
    }

    public function setNext(StepInterface $step)
    {
        $this->next = $step;
    }

    public function hasNext()
    {
        if ($this->isSkipped()) {
            return $this->next->hasNext();
        }

        return null !== $this->next;
    }
}
