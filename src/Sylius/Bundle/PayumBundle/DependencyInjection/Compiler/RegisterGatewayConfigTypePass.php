<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RegisterGatewayConfigTypePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius.form_registry.payum_gateway_config')) {
            return;
        }

        $formRegistry = $container->findDefinition('sylius.form_registry.payum_gateway_config');
        $gatewayFactories = [];

        $gatewayConfigurationTypes = $container->findTaggedServiceIds('sylius.gateway_configuration_type');

        foreach ($gatewayConfigurationTypes as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged gateway configuration type needs to have `type` and `label` attributes.');
            }

            $gatewayFactories[$attributes[0]['type']] = $attributes[0]['label'];

            $formRegistry->addMethodCall(
                'add',
                ['gateway_config', $attributes[0]['type'], $container->getDefinition($id)->getClass()]
            );
        }

        $gatewayFactories = array_merge($gatewayFactories, ['offline' => 'sylius.payum_gateway_factory.offline']);
        ksort($gatewayFactories);

        $container->setParameter('sylius.gateway_factories', $gatewayFactories);
    }
}
