<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Listener;

use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ListenerRegistry implements ListenerRegistryInterface
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param ListenerInterface $listener
     */
    public function addListener(ListenerInterface $listener)
    {
        Assert::keyNotExists($this->listeners, $listener->getName(), 'Listener with name "%s" is already registered.');

        $this->listeners[$listener->getName()] = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getListener($name)
    {
        if (!isset($this->listeners[$name])) {
            throw new ListenerNotFoundException($name);
        }

        return $this->listeners[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
