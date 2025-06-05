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

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderIntegrityChecker;
use Sylius\Bundle\ShopBundle\EventListener\OrderIntegrityCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class OrderIntegrityCheckerTest extends TestCase
{
    private MockObject&RouterInterface $router;

    private MockObject&ObjectManager $orderManager;

    private MockObject&OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker;

    private OrderIntegrityChecker $orderIntegrityChecker;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->orderManager = $this->createMock(ObjectManager::class);
        $this->orderPromotionsIntegrityChecker = $this->createMock(OrderPromotionsIntegrityCheckerInterface::class);

        $this->orderIntegrityChecker = new OrderIntegrityChecker(
            $this->router,
            $this->orderManager,
            $this->orderPromotionsIntegrityChecker,
        );
    }

    public function testImplementsOrderIntegrityCheckerInterface(): void
    {
        $this->assertInstanceOf(OrderIntegrityCheckerInterface::class, $this->orderIntegrityChecker);
    }

    public function testDoesNothingIfGivenOrderHasValidPromotionApplied(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $order->expects($this->exactly(2))->method('getTotal')->willReturnOnConsecutiveCalls(1000, 1000);
        $this->orderPromotionsIntegrityChecker->expects($this->once())->method('check')->with($order)->willReturn(null);
        $event->expects($this->never())->method('stop');
        $event->expects($this->never())->method('setResponse');

        $this->orderIntegrityChecker->check($event);
    }

    public function testStopsFutureActionIfGivenOrderHasDifferentPromotionApplied(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var PromotionInterface&MockObject $oldPromotion */
        $oldPromotion = $this->createMock(PromotionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $order->expects($this->once())->method('getTotal')->willReturn(1000);
        $oldPromotion->expects($this->once())->method('getName')->willReturn('Christmas');
        $this->router->expects($this->once())->method('generate')->with('sylius_shop_checkout_complete')->willReturn('checkout.com');
        $this->orderPromotionsIntegrityChecker->expects($this->once())->method('check')->with($order)->willReturn($oldPromotion);
        $event->expects($this->once())->method('stop')->with('sylius.order.promotion_integrity', GenericEvent::TYPE_ERROR, ['%promotionName%' => 'Christmas']);
        $event->expects($this->once())->method('setResponse')->with(new RedirectResponse('checkout.com'));
        $this->orderManager->expects($this->once())->method('persist')->with($order);
        $this->orderManager->expects($this->once())->method('flush');

        $this->orderIntegrityChecker->check($event);
    }

    public function testStopsFutureActionIfGivenOrderHasDifferentTotalValue(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $order->expects($this->exactly(2))->method('getTotal')->willReturnOnConsecutiveCalls(1000, 1500);
        $this->router->expects($this->once())->method('generate')->with('sylius_shop_checkout_complete')->willReturn('checkout.com');
        $this->orderPromotionsIntegrityChecker->expects($this->once())->method('check')->with($order)->willReturn(null);
        $event->expects($this->once())->method('stop')->with('sylius.order.total_integrity', GenericEvent::TYPE_ERROR);
        $event->expects($this->once())->method('setResponse')->with(new RedirectResponse('checkout.com'));
        $this->orderManager->expects($this->once())->method('persist')->with($order);
        $this->orderManager->expects($this->once())->method('flush');

        $this->orderIntegrityChecker->check($event);
    }

    public function testStopsFutureActionIfGivenOrderHasNoPromotionApplied(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var PromotionInterface&MockObject $promotion */
        $promotion = $this->createMock(PromotionInterface::class);
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn($order);
        $order->expects($this->once())->method('getTotal')->willReturn(1000);
        $promotion->expects($this->once())->method('getName')->willReturn('Christmas');
        $this->orderPromotionsIntegrityChecker->expects($this->once())->method('check')->with($order)->willReturn($promotion);
        $this->router->expects($this->once())->method('generate')->with('sylius_shop_checkout_complete')->willReturn('checkout.com');
        $event->expects($this->once())->method('stop')->with('sylius.order.promotion_integrity', GenericEvent::TYPE_ERROR, ['%promotionName%' => 'Christmas']);
        $event->expects($this->once())->method('setResponse')->with(new RedirectResponse('checkout.com'));
        $this->orderManager->expects($this->once())->method('persist')->with($order);
        $this->orderManager->expects($this->once())->method('flush');

        $this->orderIntegrityChecker->check($event);
    }

    public function testThrowsInvalidArgumentExceptionIfEventSubjectIsNotOrder(): void
    {
        /** @var GenericEvent&MockObject $event */
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->orderIntegrityChecker->check($event);
    }
}
