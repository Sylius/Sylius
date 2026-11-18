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

namespace Sylius\Bundle\UiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;

/**
 * This pass points the IconFinder at a dedicated Twig environment backed by the native filesystem
 * loader, so it can discover template files again without affecting runtime template rendering.
 *
 * @see https://github.com/Sylius/Sylius/issues/18712
 */
final class UxIconsIconFinderPass implements CompilerPassInterface
{
    /**
     * Symfony UX Icons 3.0 introduced a chain of icon finders, and the Twig-backed one moved from
     * ".ux_icons.icon_finder" to ".ux_icons.template_icon_finder".
     */
    private const ICON_FINDER_IDS = ['.ux_icons.template_icon_finder', '.ux_icons.icon_finder'];

    private const NATIVE_FILESYSTEM_LOADER_ID = 'twig.loader.native_filesystem';

    private const ENVIRONMENT_ID = 'sylius_ui.ux_icons.twig_environment';

    public function process(ContainerBuilder $container): void
    {
        $iconFinderId = $this->resolveIconFinderId($container);
        if (null === $iconFinderId) {
            return;
        }

        if (!$container->has(self::NATIVE_FILESYSTEM_LOADER_ID)) {
            return;
        }

        $environment = (new Definition(Environment::class, [new Reference(self::NATIVE_FILESYSTEM_LOADER_ID)]))
            ->setPublic(false)
        ;

        $container->setDefinition(self::ENVIRONMENT_ID, $environment);
        $container->getDefinition($iconFinderId)
            ->replaceArgument(0, new Reference(self::ENVIRONMENT_ID))
        ;
    }

    private function resolveIconFinderId(ContainerBuilder $container): ?string
    {
        foreach (self::ICON_FINDER_IDS as $iconFinderId) {
            if ($container->hasDefinition($iconFinderId)) {
                return $iconFinderId;
            }
        }

        return null;
    }
}
