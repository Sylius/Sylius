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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RemoveUnusedPromotionCouponListener;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Promotion\Remover\UnusedPromotionCouponRemoverInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\Marking;

final class RemoveUnusedPromotionCouponListenerTest extends TestCase
{
    private MockObject&UnusedPromotionCouponRemoverInterface $unusedPromotionCouponRemover;

    private RemoveUnusedPromotionCouponListener $listener;

    protected function setUp(): void
    {
        $this->unusedPromotionCouponRemover = $this->createMock(UnusedPromotionCouponRemoverInterface::class);
        $this->listener = new RemoveUnusedPromotionCouponListener($this->unusedPromotionCouponRemover);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $event = new TransitionEvent(new \stdClass(), new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItRemovesUnusedPromotionCoupon(): void
    {
        $order = new Order();
        $event = new TransitionEvent($order, new Marking());

        $this->unusedPromotionCouponRemover
            ->expects($this->once())
            ->method('remove')
            ->with($order)
        ;

        ($this->listener)($event);
    }
}
