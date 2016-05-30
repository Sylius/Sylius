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
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureOptionsProcessorInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactory;
use Sylius\Bundle\FixturesBundle\Suite\SuiteFactoryInterface;

/**
 * @mixin SuiteFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteFactorySpec extends ObjectBehavior
{
    function let(FixtureRegistryInterface $fixtureRegistry, FixtureOptionsProcessorInterface $fixtureOptionsProcessor)
    {
        $this->beConstructedWith($fixtureRegistry, $fixtureOptionsProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Suite\SuiteFactory');
    }

    function it_implements_suite_factory_interface()
    {
        $this->shouldImplement(SuiteFactoryInterface::class);
    }

    function it_creates_a_new_empty_suite()
    {
        $suite = $this->createSuite('suite_name', ['fixtures' => []]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldGenerate();
    }

    function it_creates_a_new_suite_with_fixtures(
        FixtureRegistryInterface $fixtureRegistry,
        FixtureOptionsProcessorInterface $fixtureOptionsProcessor,
        FixtureInterface $firstFixture,
        FixtureInterface $secondFixture
    ) {
        $fixtureRegistry->getFixture('first_fixture')->willReturn($firstFixture);
        $fixtureRegistry->getFixture('second_fixture')->willReturn($secondFixture);

        $fixtureOptionsProcessor->process($firstFixture, [[]])->willReturn([]);
        $fixtureOptionsProcessor->process($secondFixture, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['fixtures' => [
            'first_fixture' => ['options' => [[]]],
            'second_fixture' => ['options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldGenerateKeys($firstFixture, $secondFixture);
    }

    function it_creates_a_new_suite_with_prioritized_fixtures(
        FixtureRegistryInterface $fixtureRegistry,
        FixtureOptionsProcessorInterface $fixtureOptionsProcessor,
        FixtureInterface $fixture,
        FixtureInterface $higherPriorityFixture
    ) {
        $fixtureRegistry->getFixture('fixture')->willReturn($fixture);
        $fixtureRegistry->getFixture('higher_priority_fixture')->willReturn($higherPriorityFixture);

        $fixtureOptionsProcessor->process($fixture, [[]])->willReturn([]);
        $fixtureOptionsProcessor->process($higherPriorityFixture, [[]])->willReturn([]);

        $suite = $this->createSuite('suite_name', ['fixtures' => [
            'fixture' => ['priority' => 5, 'options' => [[]]],
            'higher_priority_fixture' => ['priority' => 10, 'options' => [[]]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldGenerateKeys($higherPriorityFixture, $fixture);
    }

    function it_creates_a_new_suite_with_customized_fixture(
        FixtureRegistryInterface $fixtureRegistry,
        FixtureOptionsProcessorInterface $fixtureOptionsProcessor,
        FixtureInterface $fixture
    ) {
        $fixtureRegistry->getFixture('fixture')->willReturn($fixture);

        $fixtureOptionsProcessor->process($fixture, [['fixture_option' => 'fixture_value']])->willReturn(['fixture_option' => 'fixture_value']);

        $suite = $this->createSuite('suite_name', ['fixtures' => [
            'fixture' => ['options' => [['fixture_option' => 'fixture_value']]],
        ]]);

        $suite->getName()->shouldReturn('suite_name');
        $suite->getFixtures()->shouldGenerate([$fixture, ['fixture_option' => 'fixture_value']]);
    }

    function it_throws_an_exception_if_suite_options_does_not_have_fixtures()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', []]);
    }

    function it_throws_an_exception_if_fixture_does_not_have_options_defined()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('createSuite', ['suite_name', ['fixtures' => [
            'fixture' => [],
        ]]]);
    }
}
