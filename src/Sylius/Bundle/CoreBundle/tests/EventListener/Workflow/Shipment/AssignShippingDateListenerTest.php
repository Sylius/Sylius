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
use stdClass;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\AssignShippingDateListener;
use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssignerInterface;
use Sylius\Component\Core\Model\Shipment;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class AssignShippingDateListenerTest extends TestCase
{
    private MockObject&ShippingDateAssignerInterface $shippingDateAssignerMock;

    private AssignShippingDateListener $assignShippingDateListener;

    protected function setUp(): void
    {
        $this->shippingDateAssignerMock = $this->createMock(ShippingDateAssignerInterface::class);
        $this->assignShippingDateListener = new AssignShippingDateListener($this->shippingDateAssignerMock);
    }

    public function testThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $subject = $this->createMock(stdClass::class);

        $this->expectException(InvalidArgumentException::class);

        $this->assignShippingDateListener->__invoke(new TransitionEvent($subject, new Marking()));
    }

    public function testResolvesOrderStateAfterOrderBeingShipped(): void
    {
        $shipment = new Shipment();
        $event = new TransitionEvent($shipment, new Marking());

        $this->shippingDateAssignerMock->expects($this->once())->method('assign')->with($shipment);

        $this->assignShippingDateListener->__invoke($event);
    }
}
