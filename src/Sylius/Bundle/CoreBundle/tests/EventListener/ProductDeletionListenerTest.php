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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ProductDeletionListener;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class ProductDeletionListenerTest extends TestCase
{
    private MockObject&ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker;

    private ProductDeletionListener $productDeletionListener;

    protected function setUp(): void
    {
        $this->productInPromotionRuleChecker = $this->createMock(ProductInPromotionRuleCheckerInterface::class);
        $this->productDeletionListener = new ProductDeletionListener($this->productInPromotionRuleChecker);
    }

    public function testThrowsAnExceptionWhenSubjectIsNotAProduct(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('subject');

        $this->expectException(InvalidArgumentException::class);

        $this->productDeletionListener->protectFromRemovingProductInUseByPromotionRule($event);
    }

    public function testDoesNothingWhenProductIsNotAssignedToRule(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);

        $this->productInPromotionRuleChecker
            ->expects($this->once())
            ->method('isInUse')
            ->with($product)
            ->willReturn(false)
        ;

        $event->expects($this->never())->method('setMessageType')->with('error');
        $event->expects($this->never())->method('setMessage')->with('sylius.product.in_use_by_promotion_rule');
        $event->expects($this->never())->method('stopPropagation');

        $this->productDeletionListener->protectFromRemovingProductInUseByPromotionRule($event);
    }

    public function testPreventsToRemoveProductIfItIsAssignedToRule(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);

        $this->productInPromotionRuleChecker
            ->expects($this->once())
            ->method('isInUse')
            ->with($product)
            ->willReturn(true)
        ;

        $event->expects($this->once())->method('setMessageType')->with('error');
        $event->expects($this->once())->method('setMessage')->with('sylius.product.in_use_by_promotion_rule');
        $event->expects($this->once())->method('stopPropagation');

        $this->productDeletionListener->protectFromRemovingProductInUseByPromotionRule($event);
    }
}
