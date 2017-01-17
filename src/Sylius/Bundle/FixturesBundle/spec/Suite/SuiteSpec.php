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
use Sylius\Bundle\FixturesBundle\Suite\Suite;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('suite_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Suite::class);
    }

    function it_implements_suite_interface()
    {
        $this->shouldImplement(SuiteInterface::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('suite_name');
    }

    function it_has_no_fixtures_by_default()
    {
        $this->getFixtures()->shouldIterateAs([]);
    }

    function it_allows_for_adding_a_fixture(FixtureInterface $fixture)
    {
        $this->addFixture($fixture, []);

        $this->getFixtures()->shouldHaveKey($fixture);
    }

    function it_stores_a_fixture_with_its_options(FixtureInterface $fixture)
    {
        $this->addFixture($fixture, ['fixture_option' => 'fixture_name']);

        $this->getFixtures()->shouldHaveKeyWithValue($fixture, ['fixture_option' => 'fixture_name']);
    }

    function it_stores_multiple_fixtures_as_queue(FixtureInterface $firstFixture, FixtureInterface $secondFixture)
    {
        $this->addFixture($firstFixture, []);
        $this->addFixture($secondFixture, []);

        $this->getFixtures()->shouldIterateAs($this->createGenerator($firstFixture, $secondFixture));
    }

    function it_keeps_the_priority_of_fixtures(
        FixtureInterface $regularFixture,
        FixtureInterface $higherPriorityFixture,
        FixtureInterface $lowerPriorityFixture
    ) {
        $this->addFixture($regularFixture, []);
        $this->addFixture($higherPriorityFixture, [], 10);
        $this->addFixture($lowerPriorityFixture, [], -10);

        $this->getFixtures()->shouldIterateAs($this->createGenerator($higherPriorityFixture, $regularFixture, $lowerPriorityFixture));
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
