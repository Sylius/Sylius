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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\ResolveOrderShipmentStateListener;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderShipmentStateListenerTest extends TestCase
{
    private MockObject&StateResolverInterface $stateResolver;

    private ResolveOrderShipmentStateListener $resolveOrderShipmentStateListener;

    protected function setUp(): void
    {
        $this->stateResolver = $this->createMock(StateResolverInterface::class);
        $this->resolveOrderShipmentStateListener = new ResolveOrderShipmentStateListener($this->stateResolver);
    }

    public function testThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $subject = $this->createMock(\stdClass::class);

        $this->expectException(InvalidArgumentException::class);

        $this->resolveOrderShipmentStateListener->__invoke(new CompletedEvent($subject, new Marking()));
    }

    public function testResolvesOrderShipmentStateAfterOrderBeingShipped(): void
    {
        $shipment = new Shipment();
        $order = new Order();

        $shipment->setOrder($order);

        $event = new CompletedEvent($shipment, new Marking());

        $this->stateResolver->expects($this->once())->method('resolve')->with($order);

        $this->resolveOrderShipmentStateListener->__invoke($event);
    }
}
