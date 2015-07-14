<?php

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Locator\BundleResourceLocator;
use Sylius\Bundle\ThemeBundle\Locator\PathCheckerInterface;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @mixin BundleResourceLocator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class BundleResourceLocatorSpec extends FixtureAwareObjectBehavior
{
    function let(PathCheckerInterface $pathChecker, KernelInterface $kernel)
    {
        $this->beConstructedWith($pathChecker, $kernel, "/app");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Locator\BundleResourceLocator');
    }

    function it_implements_resource_locator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface');
    }

    function it_locates_resource(PathCheckerInterface $pathChecker, KernelInterface $kernel, BundleInterface $bundle)
    {
        $bundle->getName()->willReturn("Bundle");
        $bundle->getPath()->willReturn("/app/bundle");

        $kernel->getBundle("Bundle", false)->willReturn([$bundle]);

        $pathChecker->processPaths(Argument::type('array'), Argument::type('array'), [])->shouldBeCalled()->willReturn("/app/bundle/resource");

        $this->locateResource("@Bundle/Resources/resource", [])->shouldReturn("/app/bundle/resource");
    }
}
