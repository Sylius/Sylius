<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FixturesBundle\Loader;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Listener\AfterSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Loader\HookableSuiteLoader;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class HookableSuiteLoaderSpec extends ObjectBehavior
{
    function let(SuiteLoaderInterface $decoratedSuiteLoader)
    {
        $this->beConstructedWith($decoratedSuiteLoader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Loader\HookableSuiteLoader');
    }

    function it_implements_suite_loader_interface()
    {
        $this->shouldImplement(SuiteLoaderInterface::class);
    }

    function it_delegates_suite_loading_to_the_base_loader(SuiteLoaderInterface $decoratedSuiteLoader, SuiteInterface $suite)
    {
        $suite->getListeners()->willReturn([]);

        $decoratedSuiteLoader->load($suite)->shouldBeCalled();

        $this->load($suite);
    }

    function it_executes_before_suite_listeners(
        SuiteLoaderInterface $decoratedSuiteLoader,
        SuiteInterface $suite,
        BeforeSuiteListenerInterface $beforeSuiteListener
    ) {
        $suite->getListeners()->will(function () use ($beforeSuiteListener) {
            yield $beforeSuiteListener->getWrappedObject() => [];
        });

        $beforeSuiteListener->beforeSuite(new SuiteEvent($suite->getWrappedObject()), [])->shouldBeCalledTimes(1);

        $decoratedSuiteLoader->load($suite)->shouldBeCalled();

        $this->load($suite);
    }

    function it_executes_after_suite_listeners(
        SuiteLoaderInterface $decoratedSuiteLoader,
        SuiteInterface $suite,
        AfterSuiteListenerInterface $afterSuiteListener
    ) {
        $suite->getListeners()->will(function () use ($afterSuiteListener) {
            yield $afterSuiteListener->getWrappedObject() => [];
        });

        $decoratedSuiteLoader->load($suite)->shouldBeCalled();

        $afterSuiteListener->afterSuite(new SuiteEvent($suite->getWrappedObject()), [])->shouldBeCalledTimes(1);

        $this->load($suite);
    }

    function it_executes_customized_suite_listeners(
        SuiteLoaderInterface $decoratedSuiteLoader,
        SuiteInterface $suite,
        BeforeSuiteListenerInterface $beforeSuiteListener,
        AfterSuiteListenerInterface $afterSuiteListener
    ) {
        $suite->getListeners()->will(function () use ($beforeSuiteListener, $afterSuiteListener) {
            yield $beforeSuiteListener->getWrappedObject() => ['listener_option1' => 'listener_value1'];
            yield $afterSuiteListener->getWrappedObject() => ['listener_option2' => 'listener_value2'];
        });

        $beforeSuiteListener->beforeSuite(new SuiteEvent($suite->getWrappedObject()), ['listener_option1' => 'listener_value1'])->shouldBeCalledTimes(1);

        $decoratedSuiteLoader->load($suite)->shouldBeCalled();

        $afterSuiteListener->afterSuite(new SuiteEvent($suite->getWrappedObject()), ['listener_option2' => 'listener_value2'])->shouldBeCalledTimes(1);

        $this->load($suite);
    }
}
