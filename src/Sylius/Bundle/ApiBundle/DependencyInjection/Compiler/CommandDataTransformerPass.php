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

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class CommandDataTransformerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $commandDataTransformersChainDefinition = new Definition(CommandAwareInputDataTransformer::class);

        $taggedServices = $container->findTaggedServiceIds('sylius.api.command_data_transformer');

        uasort($taggedServices, function (array $firstTag, array $secondTag) {
            return ($secondTag[0]['priority'] ?? 0) <=> ($firstTag[0]['priority'] ?? 0);
        });

        foreach ($taggedServices as $key => $attributes) {
            $commandDataTransformersChainDefinition->addArgument(new Reference($key));
        }

        $commandDataTransformersChainDefinition->addTag('api_platform.data_transformer');

        $container->setDefinition(
            'sylius.api.data_transformer.command_aware_input_data_transformer',
            $commandDataTransformersChainDefinition,
        );
    }
}
