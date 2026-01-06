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

namespace Tests\Sylius\Bundle\CoreBundle\ShippingMethod\Updater;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\ShippingMethod\Updater\ShippingMethodUpdater;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;

final class ShippingMethodUpdaterTest extends TestCase
{
    private MockObject&ShippingMethodRepositoryInterface $shippingMethodRepository;

    private EntityManagerInterface&MockObject $entityManager;

    private ShippingMethodUpdater $shippingMethodUpdater;

    protected function setUp(): void
    {
        $this->shippingMethodRepository = $this->createMock(ShippingMethodRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->shippingMethodUpdater = new ShippingMethodUpdater(
            $this->shippingMethodRepository,
            $this->entityManager,
        );
    }

    public function testRemovesChannelCodeFromShippingMethodConfiguration(): void
    {
        $shippingMethod1 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 100],
            'OTHER_CHANNEL' => ['amount' => 200],
        ]);
        $shippingMethod2 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 300],
        ]);

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('DELETED_CHANNEL')
            ->willReturn([$shippingMethod1, $shippingMethod2])
        ;

        $shippingMethod1
            ->expects(self::once())
            ->method('setConfiguration')
            ->with(['OTHER_CHANNEL' => ['amount' => 200]])
        ;

        $shippingMethod2
            ->expects(self::once())
            ->method('setConfiguration')
            ->with([])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->shippingMethodUpdater->removeChannelConfigurationFromShippingMethods('DELETED_CHANNEL');
    }

    public function testFlushesChangesEvenWhenNoShippingMethodsFound(): void
    {
        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('DELETED_CHANNEL')
            ->willReturn([])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->shippingMethodUpdater->removeChannelConfigurationFromShippingMethods('DELETED_CHANNEL');
    }

    public function testRemovesOnlySpecifiedChannelCodeFromConfiguration(): void
    {
        $shippingMethod = $this->createShippingMethodWithConfiguration([
            'CHANNEL_A' => ['amount' => 100],
            'CHANNEL_B' => ['amount' => 200],
            'CHANNEL_C' => ['amount' => 300],
        ]);

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('CHANNEL_B')
            ->willReturn([$shippingMethod])
        ;

        $shippingMethod
            ->expects(self::once())
            ->method('setConfiguration')
            ->with([
                'CHANNEL_A' => ['amount' => 100],
                'CHANNEL_C' => ['amount' => 300],
            ])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->shippingMethodUpdater->removeChannelConfigurationFromShippingMethods('CHANNEL_B');
    }

    public function testHandlesMultipleShippingMethodsWithDifferentConfigurations(): void
    {
        $shippingMethod1 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 100],
        ]);
        $shippingMethod2 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['amount' => 200, 'tax_rate' => 0.23],
            'ANOTHER_CHANNEL' => ['amount' => 300],
        ]);
        $shippingMethod3 = $this->createShippingMethodWithConfiguration([
            'DELETED_CHANNEL' => ['custom_config' => 'value'],
            'CHANNEL_X' => ['amount' => 400],
            'CHANNEL_Y' => ['amount' => 500],
        ]);

        $this->shippingMethodRepository
            ->expects(self::once())
            ->method('findByChannelCodeInConfiguration')
            ->with('DELETED_CHANNEL')
            ->willReturn([$shippingMethod1, $shippingMethod2, $shippingMethod3])
        ;

        $shippingMethod1
            ->expects(self::once())
            ->method('setConfiguration')
            ->with([])
        ;

        $shippingMethod2
            ->expects(self::once())
            ->method('setConfiguration')
            ->with(['ANOTHER_CHANNEL' => ['amount' => 300]])
        ;

        $shippingMethod3
            ->expects(self::once())
            ->method('setConfiguration')
            ->with([
                'CHANNEL_X' => ['amount' => 400],
                'CHANNEL_Y' => ['amount' => 500],
            ])
        ;

        $this->entityManager->expects(self::once())->method('flush');

        $this->shippingMethodUpdater->removeChannelConfigurationFromShippingMethods('DELETED_CHANNEL');
    }

    /** @param array<string, mixed> $configuration */
    private function createShippingMethodWithConfiguration(array $configuration): MockObject&ShippingMethodInterface
    {
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $shippingMethod->expects(self::once())->method('getConfiguration')->willReturn($configuration);

        return $shippingMethod;
    }
}
