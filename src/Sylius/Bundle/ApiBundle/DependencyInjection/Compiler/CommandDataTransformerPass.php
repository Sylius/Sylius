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

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformersChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/** @experimental */
final class CommandDataTransformerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $commandDataTransformersChainDefinition = new Definition(CommandDataTransformersChain::class);

        $taggedServices = $container->findTaggedServiceIds('sylius.api.command_data_transformer');

        foreach ($taggedServices as $key => $value) {
            $commandDataTransformersChainDefinition->addArgument(new Reference($key));
        }

        $container->setDefinition(
            'sylius_bundle_api.command_data_transformers_chain',
            $commandDataTransformersChainDefinition
        );
    }
}
