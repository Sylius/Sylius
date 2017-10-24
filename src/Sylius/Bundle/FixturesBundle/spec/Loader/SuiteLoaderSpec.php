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
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface;
use Sylius\Bundle\FixturesBundle\Loader\SuiteLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class SuiteLoaderSpec extends ObjectBehavior
{
    function let(FixtureLoaderInterface $fixtureLoader): void
    {
        $this->beConstructedWith($fixtureLoader);
    }

    function it_implements_suite_loader_interface(): void
    {
        $this->shouldImplement(SuiteLoaderInterface::class);
    }

    function it_loads_suite_fixtures(
        FixtureLoaderInterface $fixtureLoader,
        SuiteInterface $suite,
        FixtureInterface $firstFixture,
        FixtureInterface $secondFixture
    ): void {
        $suite->getFixtures()->will(function () use ($firstFixture, $secondFixture) {
            yield $firstFixture->getWrappedObject() => ['options 1'];
            yield $secondFixture->getWrappedObject() => ['options 2'];
        });

        $fixtureLoader->load($suite, $firstFixture, ['options 1'])->shouldBeCalled();
        $fixtureLoader->load($suite, $secondFixture, ['options 2'])->shouldBeCalled();

        $this->load($suite);
    }
}
