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

namespace Tests\Sylius\Bundle\OrderBundle\Resetter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetter;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class CartChangesResetterTest extends TestCase
{
    private EntityManagerInterface&MockObject $manager;

    private CartChangesResetter $cartChangesResetter;

    private MockObject&OrderInterface $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->cartChangesResetter = new CartChangesResetter($this->manager);
        $this->cart = $this->createMock(OrderInterface::class);
    }

    public function testDoesNothingIfCartIsNotManaged(): void
    {
        $this->manager->expects(self::once())
            ->method('contains')
            ->with($this->cart)
            ->willReturn(false);

        $this->manager->expects(self::never())->method('refresh')->with($this->cart);

        $this->cartChangesResetter->resetChanges($this->cart);
    }

    public function testResetsChangesForCartItemsAndUnits(): void
    {
        $item = $this->createMock(OrderItemInterface::class);
        $unitNew = $this->createMock(OrderItemUnitInterface::class);
        $unitExisting = $this->createMock(OrderItemUnitInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $this->manager
            ->expects(self::once())
            ->method('contains')
            ->with($this->cart)
            ->willReturn(true);

        $this->manager
            ->expects(self::once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $this->cart
            ->expects(self::once())
            ->method('getItems')
            ->willReturn(new ArrayCollection([$item]));

        $item
            ->expects(self::once())
            ->method('getUnits')
            ->willReturn(new ArrayCollection([$unitNew, $unitExisting]));

        $unitOfWork
            ->expects(self::exactly(2))
            ->method('getEntityState')
            ->willReturnCallback(fn ($unit) => match ($unit) {
                $unitNew => UnitOfWork::STATE_NEW,
                $unitExisting => UnitOfWork::STATE_MANAGED,
                default => throw new \UnhandledMatchError(),
            });

        $item
            ->expects(self::once())
            ->method('removeUnit')
            ->with($unitNew);

        $this->manager
            ->expects(self::exactly(2))
            ->method('refresh')
            ->willReturnCallback(function ($object) use ($item) {
                self::assertTrue($object === $item || $object === $this->cart);
            });

        $this->cartChangesResetter->resetChanges($this->cart);
    }
}
