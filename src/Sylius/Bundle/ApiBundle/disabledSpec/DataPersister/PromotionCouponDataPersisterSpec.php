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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\PromotionCouponCannotBeRemoved;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PromotionCouponDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $dataPersister): void
    {
        $this->beConstructedWith($dataPersister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_promotion_coupon(PromotionCouponInterface $coupon): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($coupon)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_promotion_coupon(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionCouponInterface $coupon,
    ): void {
        $dataPersister->persist($coupon, [])->shouldBeCalled();

        $this->persist($coupon);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionCouponInterface $coupon,
    ): void {
        $dataPersister->remove($coupon, [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(PromotionCouponCannotBeRemoved::class)->during('remove', [$coupon]);
    }

    function it_uses_inner_persister_to_remove_promotion_coupon(
        ContextAwareDataPersisterInterface $dataPersister,
        PromotionCouponInterface $coupon,
    ): void {
        $dataPersister->remove($coupon, [])->shouldBeCalled();

        $this->remove($coupon);
    }
}
