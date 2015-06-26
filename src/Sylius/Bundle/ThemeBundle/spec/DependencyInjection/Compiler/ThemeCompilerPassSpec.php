<?php

namespace spec\Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeCompilerPass;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin ThemeCompilerPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeCompilerPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeCompilerPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface');
    }

    function it_adds_themes_to_theme_repository(
        ContainerBuilder $containerBuilder,
        Definition $themeRepositoryDefinition,
        LoaderInterface $themeLoader,
        FileLocatorInterface $themeLocator,
        ThemeInterface $theme
    ) {
        $themeRepositoryDefinition->addArgument(Argument::type('array'))->shouldBeCalled();

        $themeLocator->locate("theme.json", Argument::any(), false)->shouldBeCalled()->willReturn(["foo/bar/theme.json"]);

        $themeLoader->load("foo/bar/theme.json")->shouldBeCalled()->willReturn($theme);

        $containerBuilder->findDefinition("sylius.theme.repository")->shouldBeCalled()->willReturn($themeRepositoryDefinition);
        $containerBuilder->get("sylius.theme.locator")->shouldBeCalled()->willReturn($themeLocator);
        $containerBuilder->get("sylius.theme.loader")->shouldBeCalled()->willReturn($themeLoader);
        $containerBuilder->addResource(Argument::type('Symfony\Component\Config\Resource\FileResource'))->shouldBeCalled();

        $this->process($containerBuilder);
    }

    function it_does_not_crash_if_themes_not_found(
        ContainerBuilder $containerBuilder,
        Definition $themeRepositoryDefinition,
        LoaderInterface $themeLoader,
        FileLocatorInterface $themeLocator
    ) {
        $themeLocator->locate("theme.json", Argument::any(), false)->shouldBeCalled()->willThrow(new \InvalidArgumentException());

        $containerBuilder->findDefinition("sylius.theme.repository")->shouldBeCalled()->willReturn($themeRepositoryDefinition);
        $containerBuilder->get("sylius.theme.locator")->shouldBeCalled()->willReturn($themeLocator);
        $containerBuilder->get("sylius.theme.loader")->shouldBeCalled()->willReturn($themeLoader);

        $this->process($containerBuilder);
    }
}
