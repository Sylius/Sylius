<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

final class ActivePromotionsByChannelProviderSpec extends ObjectBehavior
{
    function let(PromotionRepositoryInterface $promotionRepository): void
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_implements_an_active_promotions_provider_interface(): void
    {
        $this->shouldImplement(PreQualifiedPromotionsProviderInterface::class);
    }

    function it_provides_an_active_promotions_for_given_subject_channel(
        PromotionRepositoryInterface $promotionRepository,
        ChannelInterface $channel,
        PromotionInterface $promotion1,
        PromotionInterface $promotion2,
        OrderInterface $subject
    ): void {
        $subject->getChannel()->willReturn($channel);
        $promotionRepository->findActiveByChannel($channel)->willReturn([$promotion1, $promotion2]);

        $this->getPromotions($subject)->shouldReturn([$promotion1, $promotion2]);
    }

    function it_throws_an_exception_if_order_has_no_channel(OrderInterface $subject): void
    {
        $subject->getChannel()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Order has no channel, but it should.'))
            ->during('getPromotions', [$subject])
        ;
    }

    function it_throws_an_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject): void
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('getPromotions', [$subject]);
    }
}
