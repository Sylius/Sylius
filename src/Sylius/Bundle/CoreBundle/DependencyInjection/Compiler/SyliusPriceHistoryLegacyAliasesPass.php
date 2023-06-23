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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPriceHistoryLegacyAliasesPass implements CompilerPassInterface
{
    public const PLUGIN_TO_SYLIUS_PREFIXES_MAP = [
        'Sylius\PriceHistoryPlugin\Application\Checker\ProductVariantLowestPriceDisplayCheckerInterface' => 'Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface',
        'Sylius\PriceHistoryPlugin\Application\CommandDispatcher' => 'Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher',
        'Sylius\PriceHistoryPlugin\Application\CommandHandler' => 'Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler',
        'Sylius\PriceHistoryPlugin\Application\Logger' => 'Sylius\Bundle\CoreBundle\PriceHistory\Logger',
        'Sylius\PriceHistoryPlugin\Application\Processor' => 'Sylius\Bundle\CoreBundle\PriceHistory\Processor',
        'Sylius\PriceHistoryPlugin\Application\Remover' => 'Sylius\Bundle\CoreBundle\PriceHistory\Remover',
        'Sylius\PriceHistoryPlugin\Application\Templating\Helper\PriceHelper' => 'sylius.templating.helper.price',
        'Sylius\PriceHistoryPlugin\Domain\Factory\ChannelFactory' => 'sylius.custom_factory.channel',
        'Sylius\PriceHistoryPlugin\Infrastructure\Cli\Command\ClearPriceHistoryCommand' => 'Sylius\Bundle\CoreBundle\PriceHistory\Cli\Command\ClearPriceHistoryCommand',
        'Sylius\PriceHistoryPlugin\Infrastructure\EntityObserver' => 'Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver',
        'Sylius\PriceHistoryPlugin\Infrastructure\EventSubscriber' => 'Sylius\Bundle\CoreBundle\PriceHistory\EventSubscriber',
        'Sylius\PriceHistoryPlugin\Infrastructure\EventListener' => 'Sylius\Bundle\CoreBundle\PriceHistory\EventListener',
        'Sylius\PriceHistoryPlugin\Infrastructure\Form\Extension\ChannelTypeExtension' => 'sylius.form.extension.type.channel',
        'Sylius\PriceHistoryPlugin\Infrastructure\Form\Type\ChannelPriceHistoryConfigType' => 'sylius.form.type.channel_price_history_config',
        'Sylius\PriceHistoryPlugin\Infrastructure\Provider\ProductVariantsPricesProvider' => 'sylius.provider.product_variants_prices',
        'Sylius\PriceHistoryPlugin\Infrastructure\Twig\PriceExtension' => 'sylius.twig.extension.price',
        'sylius_price_history.controller.channel_price_history_config' => 'sylius.controller.channel_price_history_config',
        'sylius_price_history.controller.channel_pricing_log_entry' => 'sylius.controller.channel_pricing_log_entry',
        'sylius_price_history.factory.channel_price_history_config' => 'sylius.factory.channel_price_history_config',
        'sylius_price_history.factory.channel_pricing_log_entry' => 'sylius.factory.channel_pricing_log_entry',
        'sylius_price_history.form.type.channel_price_history_config' => 'sylius.form.type.channel_price_history_config',
        'sylius_price_history.form.type.channel_pricing_log_entry' => 'sylius.form.type.channel_pricing_log_entry',
        'sylius_price_history.manager.channel_price_history_config' => 'sylius.manager.channel_price_history_config',
        'sylius_price_history.manager.channel_pricing_log_entry' => 'sylius.manager.channel_pricing_log_entry',
        'sylius_price_history.repository.channel_price_history_config' => 'sylius.repository.channel_price_history_config',
        'sylius_price_history.repository.channel_pricing_log_entry' => 'sylius.repository.channel_pricing_log_entry',
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
