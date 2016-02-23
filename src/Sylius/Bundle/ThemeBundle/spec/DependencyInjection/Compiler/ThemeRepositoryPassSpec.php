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
use Sylius\Bundle\ThemeBundle\Loader\ConfigurationProviderInterface;
use Symfony\Component\Config\Resource\ResourceInterface;
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

    function it_adds_themes_definitions_to_theme_repository_constructor(
        ContainerBuilder $container,
        ConfigurationProviderInterface $configurationProvider,
        Definition $themeRepositoryDefinition,
        ResourceInterface $resource
    ) {
        $container->get('sylius.theme.configuration.provider')->willReturn($configurationProvider);

        $container->findDefinition('sylius.repository.theme')->willReturn($themeRepositoryDefinition);

        $configurationProvider->getConfigurations()->willReturn([
            ['name' => 'example/sylius-theme'],
        ]);

        $configurationProvider->getResources()->willReturn([$resource]);

        $themeDefinitionArgument = Argument::that(function (array $arguments) {
            $definition = current($arguments);

            if (!$definition instanceof Definition) {
                return false;
            }

            $arguments = $definition->getArgument(0);
            if ($arguments !== ['name' => 'example/sylius-theme']) {
                return false;
            }

            return true;
        });

        $themeRepositoryDefinition->addMethodCall('add', $themeDefinitionArgument)->shouldBeCalled();

        $container->addResource($resource)->shouldBeCalled();

        $this->process($container);
    }
}
