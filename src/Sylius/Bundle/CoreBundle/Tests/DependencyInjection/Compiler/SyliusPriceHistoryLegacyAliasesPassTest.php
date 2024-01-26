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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\SyliusPriceHistoryLegacyAliasesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPriceHistoryLegacyAliasesPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $container->register('Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface');
        $container->register('Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler\SomeService');
        $container->register('sylius.manager.channel_price_history_config');
        $container->register('sylius.factory.channel_pricing_log_entry');

        $this->process($container);

        $this->assertHasAlias($container, 'Sylius\PriceHistoryPlugin\Application\Checker\ProductVariantLowestPriceDisplayCheckerInterface');
        $this->assertHasAlias($container, 'Sylius\PriceHistoryPlugin\Application\CommandHandler\SomeService');
        $this->assertHasAlias($container, 'sylius_price_history.manager.channel_price_history_config');
        $this->assertHasAlias($container, 'sylius_price_history.factory.channel_pricing_log_entry');
    }

    private function assertHasAlias(ContainerBuilder $container, string $alias): void
    {
        $this->assertTrue($container->hasAlias($alias), 'Expected to find alias ' . $alias);
    }

    private function process(ContainerBuilder $container): void
    {
        (new SyliusPriceHistoryLegacyAliasesPass())->process($container);
    }
}
