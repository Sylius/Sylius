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

namespace Sylius\Bundle\FixturesBundle\Loader;

use Sylius\Bundle\FixturesBundle\Listener\AfterSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class HookableSuiteLoader implements SuiteLoaderInterface
{
    /**
     * @var SuiteLoaderInterface
     */
    private $decoratedSuiteLoader;

    /**
     * @param SuiteLoaderInterface $decoratedSuiteLoader
     */
    public function __construct(SuiteLoaderInterface $decoratedSuiteLoader)
    {
        $this->decoratedSuiteLoader = $decoratedSuiteLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(SuiteInterface $suite): void
    {
        $suiteEvent = new SuiteEvent($suite);

        $this->executeBeforeSuiteListeners($suite, $suiteEvent);

        $this->decoratedSuiteLoader->load($suite);

        $this->executeAfterSuiteListeners($suite, $suiteEvent);
    }

    /**
     * @param SuiteInterface $suite
     * @param SuiteEvent $suiteEvent
     */
    private function executeBeforeSuiteListeners(SuiteInterface $suite, SuiteEvent $suiteEvent): void
    {
        foreach ($suite->getListeners() as $listener => $listenerOptions) {
            if (!$listener instanceof BeforeSuiteListenerInterface) {
                continue;
            }

            $listener->beforeSuite($suiteEvent, $listenerOptions);
        }
    }

    /**
     * @param SuiteInterface $suite
     * @param SuiteEvent $suiteEvent
     */
    private function executeAfterSuiteListeners(SuiteInterface $suite, SuiteEvent $suiteEvent): void
    {
        foreach ($suite->getListeners() as $listener => $listenerOptions) {
            if (!$listener instanceof AfterSuiteListenerInterface) {
                continue;
            }

            $listener->afterSuite($suiteEvent, $listenerOptions);
        }
    }
}
