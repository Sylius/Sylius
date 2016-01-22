<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeRepositoryPass;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @mixin ThemeRepositoryPass
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeRepositoryPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeRepositoryPass');
    }

    function it_implements_compiler_pass_interface()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_adds_serialized_theme_instances_to_theme_repository_constructor(
        ContainerBuilder $container,
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        Definition $themeRepositoryDefinition,
        ThemeInterface $theme
    ) {
        $container->get('sylius.theme.configuration.provider')->willReturn($configurationProvider);
        $container->get('sylius.theme.factory')->willReturn($themeFactory);

        $container->findDefinition('sylius.theme.repository')->willReturn($themeRepositoryDefinition);

        $configurationProvider->provideAll()->willReturn([
            ['name' => 'example/sylius-theme'],
        ]);

        $themeFactory->createFromArray(['name' => 'example/sylius-theme'])->willReturn($theme);

        $themeRepositoryDefinition
            ->addMethodCall('addSerialized', [serialize($theme->getWrappedObject())])
            ->shouldBeCalled()
        ;

        $this->process($container);
    }

    function it_also_runs_process_on_configuration_provider_if_it_implements_compiler_pass_interface(
        ContainerBuilder $container,
        ConfigurationProviderInterface $configurationProvider,
        ThemeFactoryInterface $themeFactory,
        Definition $themeRepositoryDefinition,
        ThemeInterface $theme
    ) {
        /** @var ConfigurationProviderInterface|CompilerPassInterface $configurationProvider */
        $configurationProvider->implement(CompilerPassInterface::class);

        $container->get('sylius.theme.configuration.provider')->willReturn($configurationProvider);
        $container->get('sylius.theme.factory')->willReturn($themeFactory);

        $container->findDefinition('sylius.theme.repository')->willReturn($themeRepositoryDefinition);

        $configurationProvider->provideAll()->willReturn([
            ['name' => 'example/sylius-theme'],
        ]);

        $themeFactory->createFromArray(Argument::cetera())->willReturn($theme);
        $themeRepositoryDefinition->addMethodCall(Argument::cetera())->willReturn();

        $configurationProvider->process($container)->shouldBeCalled();

        $this->process($container);
    }
}
