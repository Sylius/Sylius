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

namespace spec\Sylius\Bundle\ThemeBundle\Locator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Locator\ResourceLocatorInterface;
use Sylius\Bundle\ThemeBundle\Locator\ResourceNotFoundException;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class BundleResourceLocatorSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, KernelInterface $kernel): void
    {
        $this->beConstructedWith($filesystem, $kernel);
    }

    function it_implements_resource_locator_interface(): void
    {
        $this->shouldImplement(ResourceLocatorInterface::class);
    }

    function it_locates_bundle_resource_using_path_derived_from_bundle_notation_and_symfony3_kernel_behaviour(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeInterface $theme,
        BundleInterface $childBundle,
        BundleInterface $parentBundle
    ): void {
        $kernel->getBundle('ParentBundle', false)->willReturn([$childBundle, $parentBundle]);

        $childBundle->getName()->willReturn('ChildBundle');
        $parentBundle->getName()->willReturn('ParentBundle');

        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/ChildBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(false);
        $filesystem->exists('/theme/path/ParentBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(true);

        $this->locateResource('@ParentBundle/Resources/views/Directory/index.html.twig', $theme)->shouldReturn('/theme/path/ParentBundle/views/Directory/index.html.twig');
    }

    function it_locates_bundle_resource_using_path_derived_from_bundle_notation_and_symfony4_kernel_behaviour(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeInterface $theme,
        BundleInterface $justBundle
    ): void {
        $kernel->getBundle('JustBundle', false)->willReturn($justBundle);

        $justBundle->getName()->willReturn('JustBundle');

        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/JustBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(true);

        $this->locateResource('@JustBundle/Resources/views/Directory/index.html.twig', $theme)->shouldReturn('/theme/path/JustBundle/views/Directory/index.html.twig');
    }

    function it_throws_an_exception_if_resource_can_not_be_located_using_path_derived_from_bundle_notation(
        Filesystem $filesystem,
        KernelInterface $kernel,
        ThemeInterface $theme,
        BundleInterface $childBundle,
        BundleInterface $parentBundle
    ): void {
        $kernel->getBundle('ParentBundle', false)->willReturn([$childBundle, $parentBundle]);

        $childBundle->getName()->willReturn('ChildBundle');
        $parentBundle->getName()->willReturn('ParentBundle');

        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/ChildBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(false);
        $filesystem->exists('/theme/path/ParentBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['@ParentBundle/Resources/views/Directory/index.html.twig', $theme]);
    }

    function it_locates_bundle_resource_using_path_derived_from_twig_namespaces(
        Filesystem $filesystem,
        ThemeInterface $theme
    ): void {
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/JustBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(true);

        $this->locateResource('@Just/Directory/index.html.twig', $theme)->shouldReturn('/theme/path/JustBundle/views/Directory/index.html.twig');
    }

    function it_throws_an_exception_if_resource_can_not_be_located_using_path_derived_from_twig_namespaces(
        Filesystem $filesystem,
        ThemeInterface $theme
    ): void {
        $theme->getName()->willReturn('theme/name');
        $theme->getPath()->willReturn('/theme/path');

        $filesystem->exists('/theme/path/JustBundle/views/Directory/index.html.twig')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow(ResourceNotFoundException::class)->during('locateResource', ['@Just/Directory/index.html.twig', $theme]);
    }

    function it_throws_an_exception_if_resource_path_does_not_start_with_an_asperand(ThemeInterface $theme): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateResource', ['ParentBundle/Resources/views/Directory/index.html.twig', $theme]);
    }

    function it_throws_an_exception_if_resource_path_contains_two_dots_in_a_row(ThemeInterface $theme): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('locateResource', ['@ParentBundle/Resources/views/../views/Directory/index.html.twig', $theme]);
    }
}
