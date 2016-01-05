<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Locator\ApplicationResourceLocator;
use Sylius\Bundle\ThemeBundle\Locator\PathCheckerInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;

/**
 * @mixin ApplicationResourceLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ApplicationResourceLocatorSpec extends FixtureAwareObjectBehavior
{
    function let(PathCheckerInterface $pathChecker)
    {
        $this->beConstructedWith($pathChecker, "/app");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Locator\ApplicationResourceLocator');
    }

    function it_implements_resource_locator_interface()
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_locates_resource(PathCheckerInterface $pathChecker)
    {
        $pathChecker->processPaths(Argument::type('array'), Argument::type('array'), [])->shouldBeCalled()->willReturn("/app/resource");

        $this->locateResource("resource", [])->shouldReturn("/app/resource");
    }
}
