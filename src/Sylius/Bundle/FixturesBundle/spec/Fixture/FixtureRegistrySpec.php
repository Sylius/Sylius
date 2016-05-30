<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\FixturesBundle\Fixture;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureNotFoundException;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistry;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistryInterface;

/**
 * @mixin FixtureRegistry
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureRegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Fixture\FixtureRegistry');
    }

    function it_implements_fixture_registry_interface()
    {
        $this->shouldImplement(FixtureRegistryInterface::class);
    }

    function it_has_a_fixtures(FixtureInterface $fixture)
    {
        $this->addFixture('fixture', $fixture);

        $this->getFixture('fixture')->shouldReturn($fixture);
        $this->getFixtures()->shouldReturn(['fixture' => $fixture]);
    }

    function it_saves_the_last_fixture_with_given_name(FixtureInterface $firstFixture, FixtureInterface $lastFixture)
    {
        $this->addFixture('fixture', $firstFixture);
        $this->addFixture('fixture', $lastFixture);

        $this->getFixture('fixture')->shouldReturn($lastFixture);
        $this->getFixture('fixture')->shouldNotReturn($firstFixture);
    }

    function it_returns_an_empty_fixtures_list_if_it_does_not_have_any_fixtures()
    {
        $this->getFixtures()->shouldReturn([]);
    }

    function it_throws_an_exception_if_trying_to_get_unexisting_fixture_by_name()
    {
        $this->shouldThrow(FixtureNotFoundException::class)->during('getFixture', ['fixture']);
    }
}
