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

namespace Tests\Sylius\Bundle\ShopBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderCompleteListenerTest extends TestCase
{
    private MockObject&OrderEmailManagerInterface $orderEmailManager;

    private OrderCompleteListener $orderCompleteListener;

    protected function setUp(): void
    {
        $this->orderEmailManager = $this->createMock(OrderEmailManagerInterface::class);

        $this->orderCompleteListener = new OrderCompleteListener($this->orderEmailManager);
    }

    public function testSendsAConfirmationEmail(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $this->orderEmailManager->expects($this->once())->method('sendConfirmationEmail')->with($order);
        $this->orderCompleteListener->sendConfirmationEmail($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfAnEventSubjectIsNotAnOrderInstance(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->orderCompleteListener->sendConfirmationEmail($event);
    }
}
