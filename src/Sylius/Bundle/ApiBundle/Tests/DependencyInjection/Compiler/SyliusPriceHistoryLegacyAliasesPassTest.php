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

namespace Sylius\Bundle\ApiBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\DependencyInjection\Compiler\SyliusPriceHistoryLegacyAliasesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPriceHistoryLegacyAliasesPassTest extends TestCase
{
    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $container->register('Sylius\Bundle\ApiBundle\Serializer\ChannelDenormalizer');
        $container->register('Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidatorInterface');

        $this->process($container);

        $this->assertHasAlias($container, 'Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ChannelDenormalizer');
        $this->assertHasAlias($container, 'Sylius\PriceHistoryPlugin\Application\Validator\ResourceInputDataPropertiesValidatorInterface');
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
