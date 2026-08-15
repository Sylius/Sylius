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

namespace Sylius\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Shipment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Reproduces GitHub issue #16347:
 * Doctrine error when flush() is called twice after removing shipping adjustments
 * (e.g. event listener flush + API Platform flush during order complete transition).
 */
final class OrderShippingAdjustmentDoubleFlushTest extends KernelTestCase
{
    private array $fixtures;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::$kernel->getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $this->fixtures = $fixtureLoader->load(
            [__DIR__ . '/../DataFixtures/ORM/resources/order_with_shipping_adjustment.yml'],
            [],
            [],
            PurgeMode::createDeleteMode(),
        );

        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    #[Test]
    public function it_allows_multiple_flushes_after_removing_shipping_adjustment_from_order(): void
    {
        /** @var Order $order */
        $order = $this->fixtures['order_with_shipping'];

        /** @var Shipment $shipment */
        $shipment = $this->fixtures['order_shipment'];

        $this->assertFalse(
            $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->isEmpty(),
            'Order should have a shipping adjustment before removal',
        );
        $this->assertFalse(
            $shipment->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->isEmpty(),
            'Shipment should have a shipping adjustment before removal',
        );

        $order->removeAdjustmentsRecursively(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        $this->entityManager->flush();
        $this->entityManager->flush();

        $this->assertTrue(
            $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->isEmpty(),
            'Order should have no shipping adjustments after removal',
        );
        $this->assertTrue(
            $shipment->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->isEmpty(),
            'Shipment should have no shipping adjustments after removal',
        );
    }
}
