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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Sylius\Component\Core\Translation\TranslatableEntityLocaleAssigner;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatableEntityLocalePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $translatableEntityLocaleAssignerDefinition = new Definition(TranslatableEntityLocaleAssigner::class);
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference('sylius.context.locale'));
        $translatableEntityLocaleAssignerDefinition->addArgument(new Reference('sylius.translation_locale_provider'));

        $container->setDefinition('sylius.translatable_entity_locale_assigner', $translatableEntityLocaleAssignerDefinition);
    }
}
