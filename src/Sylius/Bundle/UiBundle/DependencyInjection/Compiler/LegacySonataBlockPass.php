<?php

declare(strict_types=1);

namespace Sylius\Bundle\UiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 * @experimental
 */
final class LegacySonataBlockPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $whitelistedVariables = [];
        $configs = $container->getExtensionConfig('sonata_block');

        foreach ($configs as $config) {
            if (!isset($config['blocks']['sonata.block.service.template']['settings'])) {
                continue;
            }

            $whitelistedVariables = array_merge(
                $whitelistedVariables,
                array_keys($config['blocks']['sonata.block.service.template']['settings'])
            );
        }

        $whitelistedVariables = array_unique($whitelistedVariables);

        $container->setParameter('sylius_ui.sonata_block.whitelisted_variables', $whitelistedVariables);
    }
}
