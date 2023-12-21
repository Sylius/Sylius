<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class PaymentRequestCommandProviderPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        $commandProviders = [];
        foreach ($container->findTaggedServiceIds('sylius.api.payment_request.command_provider', true) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['id'])) {
                    $tag['id'] = $serviceId;
                }

                $factoryName = $tag['factory'] ?? null;
                Assert::notNull(
                    $factoryName,
                    sprintf('The tag "%s" must have a "factory" attribut.', 'sylius.api.payment_request.command_provider')
                );

                $type = $tag['type'] ?? null;
                Assert::notNull(
                    $type,
                    sprintf('The tag "%s" must have a "type" attribut.', 'sylius.api.payment_request.command_provider')
                );

                $commandProviders[sprintf('%s::%s', $factoryName, $type)] = new Reference($serviceId);
            }// offline::capture,
            // stripe::capture
            // stripe::status
            // stripe::refund
        }

        $container->getDefinition('sylius.api.payment_request.command_provider.locator')->addArgument($commandProviders);
    }
}
