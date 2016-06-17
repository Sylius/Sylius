<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Loader;

use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\AfterFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class HookableFixtureLoader implements FixtureLoaderInterface
{
    /**
     * @var FixtureLoaderInterface
     */
    private $decoratedFixtureLoader;

    /**
     * @param FixtureLoaderInterface $decoratedFixtureLoader
     */
    public function __construct(FixtureLoaderInterface $decoratedFixtureLoader)
    {
        $this->decoratedFixtureLoader = $decoratedFixtureLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(SuiteInterface $suite, FixtureInterface $fixture, array $options)
    {
        $fixtureEvent = new FixtureEvent($suite, $fixture, $options);

        $this->executeBeforeFixtureListeners($suite, $fixtureEvent);

        $this->decoratedFixtureLoader->load($suite, $fixture, $options);

        $this->executeAfterFixtureListeners($suite, $fixtureEvent);
    }

    /**
     * @param SuiteInterface $suite
     * @param FixtureEvent $fixtureEvent
     */
    private function executeBeforeFixtureListeners(SuiteInterface $suite, FixtureEvent $fixtureEvent)
    {
        foreach ($suite->getListeners() as $listener => $listenerOptions) {
            if (!$listener instanceof BeforeFixtureListenerInterface) {
                continue;
            }

            $listener->beforeFixture($fixtureEvent, $listenerOptions);
        }
    }

    /**
     * @param SuiteInterface $suite
     * @param FixtureEvent $fixtureEvent
     */
    private function executeAfterFixtureListeners(SuiteInterface $suite, FixtureEvent $fixtureEvent)
    {
        foreach ($suite->getListeners() as $listener => $listenerOptions) {
            if (!$listener instanceof AfterFixtureListenerInterface) {
                continue;
            }

            $listener->afterFixture($fixtureEvent, $listenerOptions);
        }
    }
}
