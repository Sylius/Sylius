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
use Sylius\Bundle\ShopBundle\EventListener\OrderLocaleAssigner;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class OrderLocaleAssignerTest extends TestCase
{
    private LocaleContextInterface&MockObject $localeContext;

    private OrderLocaleAssigner $orderLocaleAssigner;

    protected function setUp(): void
    {
        $this->localeContext = $this->createMock(LocaleContextInterface::class);

        $this->orderLocaleAssigner = new OrderLocaleAssigner($this->localeContext);
    }

    public function testAssignsLocaleToAnOrder(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('pl_PL');
        $order->expects($this->once())->method('setLocaleCode')->with('pl_PL');

        $this->orderLocaleAssigner->assignLocale($event);
    }

    public function testThrowsInvalidArgumentExceptionIfSubjectItNotOrder(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->orderLocaleAssigner->assignLocale($event);
    }
}
