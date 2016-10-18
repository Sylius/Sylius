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
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoader;
use Sylius\Bundle\FixturesBundle\Loader\FixtureLoaderInterface;
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FixtureLoaderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\FixturesBundle\Loader\FixtureLoader');
    }

    function it_implements_fixture_loader_interface()
    {
        $this->shouldImplement(FixtureLoaderInterface::class);
    }

    function it_loads_a_fixture(SuiteInterface $suite, FixtureInterface $fixture)
    {
        $fixture->load(['options'])->shouldBeCalled();

        $this->load($suite, $fixture, ['options']);
    }
}
