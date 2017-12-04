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

namespace Sylius\Bundle\FixturesBundle\Suite;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Zend\Stdlib\SplPriorityQueue;

final class Suite implements SuiteInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var SplPriorityQueue
     */
    private $fixtures;

    /**
     * @var SplPriorityQueue
     */
    private $listeners;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->fixtures = new SplPriorityQueue();
        $this->listeners = new SplPriorityQueue();
    }

    /**
     * @param FixtureInterface $fixture
     * @param array $options
     * @param int $priority
     */
    public function addFixture(FixtureInterface $fixture, array $options, int $priority = 0): void
    {
        $this->fixtures->insert(['fixture' => $fixture, 'options' => $options], $priority);
    }

    /**
     * @param ListenerInterface $listener
     * @param array $options
     * @param int $priority
     */
    public function addListener(ListenerInterface $listener, array $options, int $priority = 0): void
    {
        $this->listeners->insert(['listener' => $listener, 'options' => $options], $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures(): iterable
    {
        $fixtures = clone $this->fixtures;
        foreach ($fixtures as $fixture) {
            yield $fixture['fixture'] => $fixture['options'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners(): iterable
    {
        $listeners = clone $this->listeners;
        foreach ($listeners as $listener) {
            yield $listener['listener'] => $listener['options'];
        }
    }
}
