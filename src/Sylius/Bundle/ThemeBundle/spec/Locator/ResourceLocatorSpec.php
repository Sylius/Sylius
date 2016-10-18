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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocator;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ResourceLocatorSpec extends ObjectBehavior
{
    function let(
        ResourceLocatorInterface $applicationResourceLocator,
        ResourceLocatorInterface $bundleResourceLocator
    ) {
        $this->beConstructedWith($applicationResourceLocator, $bundleResourceLocator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceLocator::class);
    }

    function it_implements_resource_locator_interface()
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_proxies_locating_resource_to_bundle_resource_locator_if_resource_path_starts_with_an_asperand(
        ResourceLocatorInterface $applicationResourceLocator,
        ResourceLocatorInterface $bundleResourceLocator,
        ThemeInterface $theme
    ) {
        $applicationResourceLocator->locateResource(Argument::cetera())->shouldNotBeCalled();

        $bundleResourceLocator->locateResource('@AcmeBundle/Resources/resource', $theme)->shouldBeCalled();

        $this->locateResource('@AcmeBundle/Resources/resource', $theme);
    }

    function it_proxies_locating_resource_to_application_resource_locator_if_resource_path_does_not_start_with_an_asperand(
        ResourceLocatorInterface $applicationResourceLocator,
        ResourceLocatorInterface $bundleResourceLocator,
        ThemeInterface $theme
    ) {
        $bundleResourceLocator->locateResource(Argument::cetera())->shouldNotBeCalled();

        $applicationResourceLocator->locateResource('AcmeBundle/resource', $theme)->shouldBeCalled();

        $this->locateResource('AcmeBundle/resource', $theme);
    }
}
