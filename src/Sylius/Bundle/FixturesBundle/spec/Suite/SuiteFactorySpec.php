<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FixturesBundle\Suite;

use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerInterface;
use Sylius\Bundle\FixturesBundle\Listener\ListenerRegistryInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactory;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactoryInterface;
use Symfony\Component\Config\Definition\Processor;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteFactorySpec extends ObjectBehavior
{
    function let(FixtureRegistryInterface $fixtureRegistry, ListenerRegistryInterface $listenerRegistry, Processor $optionsProcessor)
    {
        $this->beConstructedWith($fixtureRegistry, $listenerRegistry, $optionsProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SuiteFactory::class);
    }

    function it_implements_suite_factory_interface()
    {
        $this->shouldImplement(SuiteFactoryInterface::class);
    }

    function it_creates_a_new_empty_suite()
    {
        $suite = $this->createSuite('suite_name', ['listeners' => [], 'fixtures' => []]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldIterateAs([]);
    }

    function it_creates_a_new_suite_with_fixtures(
        FixtureRegistryInterface $fixtureRegistry,
        Processor $optionsProcessor,
        FixtureInterface $firstFixture,
        FixtureInterface $secondFixture
    ) {
        $fixtureRegistry->getFixture('first_fixture')->willReturn($firstFixture);
        $fixtureRegistry->getFixture('second_fixture')->willReturn($secondFixture);

        $optionsProcessor->processConfiguration($firstFixture, [[]])->willReturn([]);
        $optionsProcessor->processConfiguration($secondFixture, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['listeners' => [], 'fixtures' => [
            'first_fixture' => ['name' => 'first_fixture', 'options' => [[]]],
            'second_fixture' => ['name' => 'second_fixture', 'options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldIterateAs($this->createGenerator($firstFixture, $secondFixture));
    }

    function it_creates_a_new_suite_with_fixtures_based_on_its_name_rather_than_alias(
        FixtureRegistryInterface $fixtureRegistry,
        Processor $optionsProcessor,
        FixtureInterface $fixture
    ) {
        $fixtureRegistry->getFixture('fixture_name')->shouldBeCalled()->willReturn($fixture);
        $fixtureRegistry->getFixture('fixture_alias')->shouldNotBeCalled();

        $optionsProcessor->processConfiguration($fixture, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['listeners' => [], 'fixtures' => [
            'fixture_alias' => ['name' => 'fixture_name', 'options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldIterateAs($this->createGenerator($fixture));
    }

    function it_creates_a_new_suite_with_prioritized_fixtures(
        FixtureRegistryInterface $fixtureRegistry,
        Processor $optionsProcessor,
        FixtureInterface $fixture,
        FixtureInterface $higherPriorityFixture
    ) {
        $fixtureRegistry->getFixture('fixture')->willReturn($fixture);
        $fixtureRegistry->getFixture('higher_priority_fixture')->willReturn($higherPriorityFixture);

        $optionsProcessor->processConfiguration($fixture, [[]])->willReturn([]);
        $optionsProcessor->processConfiguration($higherPriorityFixture, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['listeners' => [], 'fixtures' => [
            'fixture' => ['name' => 'fixture', 'priority' => 5, 'options' => [[]]],
            'higher_priority_fixture' => ['name' => 'higher_priority_fixture', 'priority' => 10, 'options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldIterateAs($this->createGenerator($higherPriorityFixture, $fixture));
    }

    function it_creates_a_new_suite_with_customized_fixture(
        FixtureRegistryInterface $fixtureRegistry,
        Processor $optionsProcessor,
        FixtureInterface $fixture
    ) {
        $fixtureRegistry->getFixture('fixture')->willReturn($fixture);

        $optionsProcessor->processConfiguration($fixture, [['fixture_option' => 'fixture_value']])->willReturn(['fixture_option' => 'fixture_value']);

        $suite = $this->createSuite('suite_name', ['listeners' => [], 'fixtures' => [
            'fixture' => ['name' => 'fixture', 'options' => [['fixture_option' => 'fixture_value']]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldHaveKeyWithValue($fixture, ['fixture_option' => 'fixture_value']);
    }

    function it_creates_a_new_suite_with_listeners(
        ListenerRegistryInterface $listenerRegistry,
        Processor $optionsProcessor,
        ListenerInterface $firstListener,
        ListenerInterface $secondListener
    ) {
        $listenerRegistry->getListener('first_listener')->willReturn($firstListener);
        $listenerRegistry->getListener('second_listener')->willReturn($secondListener);

        $optionsProcessor->processConfiguration($firstListener, [[]])->willReturn([]);
        $optionsProcessor->processConfiguration($secondListener, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['fixtures' => [], 'listeners' => [
            'first_listener' => ['options' => [[]]],
            'second_listener' => ['options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getListeners()->shouldIterateAs($this->createGenerator($firstListener, $secondListener));
    }

    function it_creates_a_new_suite_with_prioritized_listeners(
        ListenerRegistryInterface $listenerRegistry,
        Processor $optionsProcessor,
        ListenerInterface $listener,
        ListenerInterface $higherPriorityListener
    ) {
        $listenerRegistry->getListener('listener')->willReturn($listener);
        $listenerRegistry->getListener('higher_priority_listener')->willReturn($higherPriorityListener);

        $optionsProcessor->processConfiguration($listener, [[]])->willReturn([]);
        $optionsProcessor->processConfiguration($higherPriorityListener, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['fixtures' => [], 'listeners' => [
            'listener' => ['priority' => 5, 'options' => [[]]],
            'higher_priority_listener' => ['priority' => 10, 'options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getListeners()->shouldIterateAs($this->createGenerator($higherPriorityListener, $listener));
    }

    function it_creates_a_new_suite_with_customized_listener(
        ListenerRegistryInterface $listenerRegistry,
        Processor $optionsProcessor,
        ListenerInterface $listener
    ) {
        $listenerRegistry->getListener('listener')->willReturn($listener);

        $optionsProcessor->processConfiguration($listener, [['listener_option' => 'listener_value']])->willReturn(['listener_option' => 'listener_value']);

        $suite = $this->createSuite('suite_name', ['fixtures' => [], 'listeners' => [
            'listener' => ['options' => [['listener_option' => 'listener_value']]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getListeners()->shouldHaveKeyWithValue($listener, ['listener_option' => 'listener_value']);
    }

    function it_throws_an_exception_if_suite_options_does_not_have_fixtures()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['listeners' => []]]);
    }

    function it_throws_an_exception_if_suite_options_does_not_have_listeners()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['fixtures' => []]]);
    }

    function it_throws_an_exception_if_fixture_does_not_have_options_defined()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['listeners' => [], 'fixtures' => [
            'fixture' => ['name' => 'fixture'],
        ]]]);
    }

    function it_throws_an_exception_if_fixture_does_not_have_name_defined()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['listeners' => [], 'fixtures' => [
            'fixture' => ['options' => []],
        ]]]);
    }

    function it_throws_an_exception_if_listener_does_not_have_options_defined()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['fixtures' => [], 'listeners' => [
            'listener' => [],
        ]]]);
    }

    /**
     * @param Collaborator[] ...$collaborators
     *
     * @return \Generator
     */
    private function createGenerator(Collaborator ...$collaborators) {
        foreach ($collaborators as $collaborator) {
            yield $collaborator->getWrappedObject() => [];
        }
    }
}
