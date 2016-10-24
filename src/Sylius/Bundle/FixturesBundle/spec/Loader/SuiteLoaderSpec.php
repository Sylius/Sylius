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
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoader;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SuiteLoaderSpec extends ObjectBehavior
{
    function let(FixtureLoaderInterface $fixtureLoader)
    {
        $this->beConstructedWith($fixtureLoader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Loader\SuiteLoader');
    }

    function it_implements_suite_loader_interface()
    {
        $this->shouldImplement(SuiteLoaderInterface::class);
    }

    function it_loads_suite_fixtures(
        FixtureLoaderInterface $fixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $firstFixture,
        FixtureInterface $secondFixture
    ) {
        $suite->getFixtures()->will(function () use ($firstFixture, $secondFixture) {
            yield $firstFixture->getWrappedObject() => ['options 1'];
            yield $secondFixture->getWrappedObject() => ['options 2'];
        });

        $fixtureLoader->load($suite, $firstFixture, ['options 1'])->shouldBeCalled();
        $fixtureLoader->load($suite, $secondFixture, ['options 2'])->shouldBeCalled();

        $this->load($suite);
    }
}
