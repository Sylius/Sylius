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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\SimpleProductLockingListener;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class SimpleProductLockingListenerTest extends TestCase
{
    private EntityManagerInterface&MockObject $manager;

    private SimpleProductLockingListener $simpleProductLockingListener;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->simpleProductLockingListener = new SimpleProductLockingListener($this->manager);
    }

    public function testLocksVariantOfASimpleProductEntity(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);
        $product->expects($this->once())->method('isSimple')->willReturn(true);
        $product->expects($this->once())->method('getVariants')->willReturn(new ArrayCollection([$productVariant]));
        $productVariant->expects($this->once())->method('getVersion')->willReturn(7);

        $this->manager->lock($productVariant, LockMode::OPTIMISTIC, 7);

        $this->simpleProductLockingListener->lock($event);
    }

    public function testDoesNotLockVariantOfAConfigurableProductEntity(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($product);
        $product->expects($this->once())->method('isSimple')->willReturn(false);

        $this->simpleProductLockingListener->lock($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfEventSubjectIsNotAProduct(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->simpleProductLockingListener->lock($event);
    }
}
