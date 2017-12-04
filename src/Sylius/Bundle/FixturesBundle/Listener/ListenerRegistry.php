<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\FixturesBundle\Listener;

use Webmozart\Assert\Assert;

final class ListenerRegistry implements ListenerRegistryInterface
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param ListenerInterface $listener
     */
    public function addListener(ListenerInterface $listener): void
    {
        Assert::keyNotExists($this->listeners, $listener->getName(), 'Listener with name "%s" is already registered.');

        $this->listeners[$listener->getName()] = $listener;
    }

    /**
     * {@inheritdoc}
     */
    public function getListener(string $name): ListenerInterface
    {
        if (!isset($this->listeners[$name])) {
            throw new ListenerNotFoundException($name);
        }

        return $this->listeners[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners(): array
    {
        return $this->listeners;
    }
}
