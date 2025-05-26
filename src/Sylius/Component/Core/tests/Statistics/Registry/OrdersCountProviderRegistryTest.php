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

namespace Tests\Sylius\Component\Core\Statistics\Registry;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\OrdersCountProviderInterface;
use Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistry;
use Sylius\Component\Core\Statistics\Registry\OrdersCountProviderRegistryInterface;

final class OrdersCountProviderRegistryTest extends TestCase
{
    private MockObject&OrdersCountProviderInterface $firstProvider;

    private MockObject&OrdersCountProviderInterface $secondProvider;

    private OrdersCountProviderRegistryInterface $registry;

    protected function setUp(): void
    {
        $this->firstProvider = $this->createMock(OrdersCountProviderInterface::class);
        $this->secondProvider = $this->createMock(OrdersCountProviderInterface::class);

        $this->registry = new OrdersCountProviderRegistry(new \ArrayIterator([
            'first' => $this->firstProvider,
            'second' => $this->secondProvider,
        ]));
    }

    public function testThrowsExceptionWhenProviderWithGivenTypeDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->registry->getByType('dummy');
    }

    public function testReturnsRegisteredProviderByType(): void
    {
        $this->assertSame($this->secondProvider, $this->registry->getByType('second'));
    }
}
