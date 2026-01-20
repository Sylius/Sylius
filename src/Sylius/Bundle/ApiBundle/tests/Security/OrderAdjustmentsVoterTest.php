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

namespace Tests\Sylius\Bundle\ApiBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface;
use Sylius\Bundle\ApiBundle\Security\OrderAdjustmentsVoter;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class OrderAdjustmentsVoterTest extends TestCase
{
    private AdjustmentOrderProviderInterface&MockObject $adjustmentOrderProvider;

    private OrderAdjustmentsVoter $orderAdjustmentsVoter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adjustmentOrderProvider = $this->createMock(AdjustmentOrderProviderInterface::class);
        $this->orderAdjustmentsVoter = new OrderAdjustmentsVoter($this->adjustmentOrderProvider);
    }

    public function testOnlySupportsSyliusOrderAdjustmentAttribute(): void
    {
        self::assertTrue(
            $this->orderAdjustmentsVoter->supportsAttribute(OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT),
        );

        self::assertFalse($this->orderAdjustmentsVoter->supportsAttribute('OTHER_ATTRIBUTE'));
    }

    public function testVotesGrantedWhenCollectionIsEmpty(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                new ArrayCollection(),
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesGrantedWhenCollectionIsAnEmptyArray(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                [],
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesGrantedWhenOrderUserMatchesTokenUser(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $user = new ShopUser();
        $collection = new ArrayCollection([$adjustmentMock]);

        $this->adjustmentOrderProvider
            ->method('provide')
            ->willReturn($orderMock);

        $tokenMock->method('getUser')->willReturn($user);

        $orderMock->method('getUser')->willReturn($user);

        $orderMock->method('isCreatedByGuest')->willReturn(false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesDeniedWhenOrderUserDoesNotMatchTokenUser(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $tokenUser = new ShopUser();
        $orderUser = new ShopUser();
        $collection = new ArrayCollection([$adjustmentMock]);

        $this->adjustmentOrderProvider
            ->method('provide')
            ->willReturn($orderMock);

        $tokenMock->method('getUser')->willReturn($tokenUser);

        $orderMock->method('getUser')->willReturn($orderUser);

        $orderMock->method('isCreatedByGuest')->willReturn(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesGrantedWhenBothOrderAndTokenHaveNoUser(): void
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $collection = new ArrayCollection([$adjustmentMock]);

        $this->adjustmentOrderProvider
            ->method('provide')
            ->willReturn($orderMock);

        $tokenMock->method('getUser')->willReturn(null);

        $orderMock->method('getUser')->willReturn(null);

        $orderMock->method('isCreatedByGuest')->willReturn(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesGrantedWhenAdjustmentHasNoOrder(): void
    {
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        $collection = new ArrayCollection([$adjustmentMock]);

        $tokenMock->expects(self::once())->method('getUser')->willReturn(null);

        $this->adjustmentOrderProvider->expects(self::once())
            ->method('provide')
            ->with($adjustmentMock)
            ->willReturn(null);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesGrantedWhenAdjustmentHasOrderCreatedByGuestAndTokenHasUser(): void
    {
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $collection = new ArrayCollection([$adjustmentMock]);
        $user = new ShopUser();

        $tokenMock->expects(self::once())->method('getUser')->willReturn($user);

        $this->adjustmentOrderProvider->expects(self::atLeastOnce())
            ->method('provide')
            ->with($adjustmentMock)
            ->willReturn($orderMock);

        $orderMock->expects(self::once())->method('getUser')->willReturn(null);

        $orderMock->expects(self::once())->method('isCreatedByGuest')->willReturn(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }

    public function testVotesDeniedWhenOrderHasUserAndTokenHasNoUser(): void
    {
        /** @var TokenInterface|MockObject $tokenMock */
        $tokenMock = $this->createMock(TokenInterface::class);
        /** @var AdjustmentInterface|MockObject $adjustmentMock */
        $adjustmentMock = $this->createMock(AdjustmentInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $collection = new ArrayCollection([$adjustmentMock]);
        $user = new ShopUser();

        $tokenMock->expects(self::once())->method('getUser')->willReturn(null);

        $this->adjustmentOrderProvider->expects(self::atLeastOnce())
            ->method('provide')
            ->with($adjustmentMock)
            ->willReturn($orderMock);

        $orderMock->expects(self::atLeastOnce())
            ->method('getUser')
            ->willReturn($user);

        $orderMock->expects(self::once())->method('isCreatedByGuest')->willReturn(true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $this->orderAdjustmentsVoter->vote(
                $tokenMock,
                $collection,
                [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT],
            ),
        );
    }
}
