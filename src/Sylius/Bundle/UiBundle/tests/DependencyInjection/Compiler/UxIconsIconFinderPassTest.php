<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Bundle\UiBundle\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\UiBundle\DependencyInjection\Compiler\UxIconsIconFinderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class UxIconsIconFinderPassTest extends AbstractCompilerPassTestCase
{
    public function testItPointsTheIconFinderAtAnEnvironmentBackedByTheNativeFilesystemLoader(): void
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition(FilesystemLoader::class));

        $iconFinder = new Definition();
        $iconFinder->setArguments([new Reference('twig'), '/app/assets/icons']);
        $this->setDefinition('.ux_icons.icon_finder', $iconFinder);

        $this->compile();

        $this->assertContainerBuilderHasService('sylius_ui.ux_icons.twig_environment', Environment::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'sylius_ui.ux_icons.twig_environment',
            0,
            new Reference('twig.loader.native_filesystem'),
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            '.ux_icons.icon_finder',
            0,
            new Reference('sylius_ui.ux_icons.twig_environment'),
        );
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            '.ux_icons.icon_finder',
            1,
            '/app/assets/icons',
        );
    }

    public function testItDoesNothingWhenTheIconFinderIsNotRegistered(): void
    {
        $this->setDefinition('twig.loader.native_filesystem', new Definition(FilesystemLoader::class));

        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius_ui.ux_icons.twig_environment');
    }

    public function testItDoesNothingWhenTheNativeFilesystemLoaderIsNotRegistered(): void
    {
        $iconFinder = new Definition();
        $iconFinder->setArguments([new Reference('twig'), '/app/assets/icons']);
        $this->setDefinition('.ux_icons.icon_finder', $iconFinder);

        $this->compile();

        $this->assertContainerBuilderNotHasService('sylius_ui.ux_icons.twig_environment');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            '.ux_icons.icon_finder',
            0,
            new Reference('twig'),
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new UxIconsIconFinderPass());
    }
}
