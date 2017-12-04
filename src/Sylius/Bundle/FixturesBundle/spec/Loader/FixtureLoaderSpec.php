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
use Sylius\Bundle\FixturesBundle\Suite\SuiteInterface;

final class FixtureLoaderSpec extends ObjectBehavior
{
    function it_implements_fixture_loader_interface(): void
    {
        $this->shouldImplement(FixtureLoaderInterface::class);
    }

    function it_loads_a_fixture(SuiteInterface $suite, FixtureInterface $fixture): void
    {
        $fixture->load(['options'])->shouldBeCalled();

        $this->load($suite, $fixture, ['options']);
    }
}
