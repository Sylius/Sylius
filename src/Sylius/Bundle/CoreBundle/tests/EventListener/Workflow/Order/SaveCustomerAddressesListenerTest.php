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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SaveCustomerAddressesListener;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class SaveCustomerAddressesListenerTest extends TestCase
{
    private MockObject&OrderAddressesSaverInterface $orderAddressesSaver;

    private SaveCustomerAddressesListener $listener;

    protected function setUp(): void
    {
        $this->orderAddressesSaver = $this->createMock(OrderAddressesSaverInterface::class);
        $this->listener = new SaveCustomerAddressesListener($this->orderAddressesSaver);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItSavesAddresses(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->orderAddressesSaver->expects($this->once())->method('saveAddresses')->with($order);

        ($this->listener)($event);
    }
}
