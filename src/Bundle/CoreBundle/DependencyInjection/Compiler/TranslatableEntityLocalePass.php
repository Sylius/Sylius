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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Component\Core\Translation\TranslatableEntityLocaleAssigner;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatableEntityLocalePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $translatableEntityLocaleAssignerDefinition = new Definition(TranslatableEntityLocaleAssigner::class);
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference('sylius.context.locale'));
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference('sylius.translation_locale_provider'));
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference('Sylius\Component\Core\Checker\CLIContextCheckerInterface'));

        $container
            ->setDefinition('sylius.translatable_entity_locale_assigner', $translatableEntityLocaleAssignerDefinition)
            ->setPublic(true)
        ;
    }
}
