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

namespace spec\Sylius\Bundle\FixturesBundle\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\AfterFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class HookableFixtureLoaderSpec extends ObjectBehavior
{
    function let(FixtureLoaderInterface $decoratedFixtureLoader): void
    {
        $this->beConstructedWith($decoratedFixtureLoader);
    }

    function it_implements_fixture_loader_interface(): void
    {
        $this->shouldImplement(FixtureLoaderInterface::class);
    }

    function it_delegates_fixture_loading_to_the_base_loader(
        FixtureLoaderInterface $decoratedFixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $fixture
    ): void {
        $suite->getListeners()->willReturn([]);

        $decoratedFixtureLoader->load($suite, $fixture, ['fixture_option' => 'fixture_value'])->shouldBeCalled();

        $this->load($suite, $fixture, ['fixture_option' => 'fixture_value']);
    }

    function it_executes_before_fixture_listeners(
        FixtureLoaderInterface $decoratedFixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $fixture,
        BeforeFixtureListenerInterface $beforeFixtureListener
    ): void {
        $suite->getListeners()->will(function () use ($beforeFixtureListener) {
            yield $beforeFixtureListener->getWrappedObject() => [];
        });

        $beforeFixtureListener->beforeFixture(new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), ['fixture_option' => 'fixture_value']), [])->shouldBeCalledTimes(1);

        $decoratedFixtureLoader->load($suite, $fixture, ['fixture_option' => 'fixture_value'])->shouldBeCalled();

        $this->load($suite, $fixture, ['fixture_option' => 'fixture_value']);
    }

    function it_executes_after_fixture_listeners(
        FixtureLoaderInterface $decoratedFixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $fixture,
        AfterFixtureListenerInterface $afterFixtureListener
    ): void {
        $suite->getListeners()->will(function () use ($afterFixtureListener) {
            yield $afterFixtureListener->getWrappedObject() => [];
        });

        $decoratedFixtureLoader->load($suite, $fixture, ['fixture_option' => 'fixture_value'])->shouldBeCalled();

        $afterFixtureListener->afterFixture(new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), ['fixture_option' => 'fixture_value']), [])->shouldBeCalledTimes(1);

        $this->load($suite, $fixture, ['fixture_option' => 'fixture_value']);
    }

    function it_executes_customized_fixture_listeners(
        FixtureLoaderInterface $decoratedFixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $fixture,
        BeforeFixtureListenerInterface $beforeFixtureListener,
        AfterFixtureListenerInterface $afterFixtureListener
    ): void {
        $suite->getListeners()->will(function () use ($beforeFixtureListener, $afterFixtureListener) {
            yield $beforeFixtureListener->getWrappedObject() => ['listener_option1' => 'listener_value1'];
            yield $afterFixtureListener->getWrappedObject() => ['listener_option2' => 'listener_value2'];
        });

        $fixtureEvent = new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), ['fixture_option' => 'fixture_value']);

        $beforeFixtureListener->beforeFixture($fixtureEvent, ['listener_option1' => 'listener_value1'])->shouldBeCalledTimes(1);

        $decoratedFixtureLoader->load($suite, $fixture, ['fixture_option' => 'fixture_value'])->shouldBeCalled();

        $afterFixtureListener->afterFixture($fixtureEvent, ['listener_option2' => 'listener_value2'])->shouldBeCalledTimes(1);

        $this->load($suite, $fixture, ['fixture_option' => 'fixture_value']);
    }
}
