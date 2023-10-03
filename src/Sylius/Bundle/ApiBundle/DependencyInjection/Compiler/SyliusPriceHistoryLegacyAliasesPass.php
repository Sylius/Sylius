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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPriceHistoryLegacyAliasesPass implements CompilerPassInterface
{
    public const PLUGIN_TO_SYLIUS_PREFIXES_MAP = [
        'Sylius\PriceHistoryPlugin\Application\Validator\ResourceInputDataPropertiesValidatorInterface' => 'Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidatorInterface',
        'Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ChannelDenormalizer' => 'Sylius\Bundle\ApiBundle\Serializer\ChannelDenormalizer',
        'Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ChannelPriceHistoryConfigDenormalizer' => 'Sylius\Bundle\ApiBundle\Serializer\ChannelPriceHistoryConfigDenormalizer',
        'Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ProductVariantNormalizer' => 'Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer',
    ];

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $serviceName => $definition) {
            if ($this->shouldHaveLegacyAlias($container, $serviceName)) {
                $this->addLegacyAlias($container, $serviceName);
            }
        }
    }

    private function shouldHaveLegacyAlias(ContainerBuilder $container, string $serviceName): bool
    {
        foreach (self::PLUGIN_TO_SYLIUS_PREFIXES_MAP as $legacyPrefix => $syliusPrefix) {
            $legacyServiceName = $this->getLegacyServiceName($serviceName);

            if (str_starts_with($serviceName, $syliusPrefix) && !$container->has($legacyServiceName)) {
                return true;
            }
        }

        return false;
    }

    private function addLegacyAlias(ContainerBuilder $container, string $serviceName): void
    {
        $legacyServiceName = $this->getLegacyServiceName($serviceName);

        $container
            ->setAlias($legacyServiceName, $serviceName)
            ->setPublic(true)
            ->setDeprecated(
                'sylius/sylius',
                '1.13',
                sprintf('Alias %%alias_id%% is deprecated. Consider using the %s service.', $serviceName),
            )
        ;
    }

    private function getLegacyServiceName(string $name): string
    {
        return str_replace(
            array_values(self::PLUGIN_TO_SYLIUS_PREFIXES_MAP),
            array_keys(self::PLUGIN_TO_SYLIUS_PREFIXES_MAP),
            $name,
        );
    }
}
