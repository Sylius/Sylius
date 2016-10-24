<?php

namespace spec\Sylius\Bundle\FixturesBundle\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeFixtureListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\BeforeSuiteListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\FixtureEvent;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\LoggerListener;
use Sylius\Bundle\FixturesBundle\Listener\SuiteEvent;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LoggerListenerSpec extends ObjectBehavior
{
    function let(LoggerInterface $logger)
    {
        $this->beConstructedWith($logger);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Listener\LoggerListener');
    }

    function it_implements_listener_interface()
    {
        $this->shouldImplement(ListenerInterface::class);
    }

    function it_listens_for_before_suite_events()
    {
        $this->shouldImplement(BeforeSuiteListenerInterface::class);
    }

    function it_listens_for_before_fixture_events()
    {
        $this->shouldImplement(BeforeFixtureListenerInterface::class);
    }

    function it_logs_suite_name_on_before_suite_event(LoggerInterface $logger, SuiteInterface $suite)
    {
        $suite->getName()->willReturn('uber_suite');

        $logger->notice(Argument::that(function ($argument) use ($suite) {
            return false !== strpos($argument, $suite->getWrappedObject()->getName());
        }))->shouldBeCalled();

        $this->beforeSuite(new SuiteEvent($suite->getWrappedObject()), []);
    }

    function it_logs_fixture_name_on_before_fixture_event(LoggerInterface $logger, SuiteInterface $suite, FixtureInterface $fixture)
    {
        $fixture->getName()->willReturn('uber_fixture');

        $logger->notice(Argument::that(function ($argument) use ($fixture) {
            return false !== strpos($argument, $fixture->getWrappedObject()->getName());
        }))->shouldBeCalled();

        $this->beforeFixture(new FixtureEvent($suite->getWrappedObject(), $fixture->getWrappedObject(), []), []);
    }
}
