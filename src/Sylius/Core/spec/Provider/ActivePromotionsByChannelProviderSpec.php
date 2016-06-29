<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Channel\Model\ChannelInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\PromotionInterface;
use Sylius\Core\Repository\PromotionRepositoryInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActivePromotionsByChannelProviderSpec extends ObjectBehavior
{
    function let(PromotionRepositoryInterface $promotionRepository)
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\Provider\ActivePromotionsByChannelProvider');
    }

    function it_implements_active_promotions_provider_interface()
    {
        $this->shouldImplement(PreQualifiedPromotionsProviderInterface::class);
    }

    function it_provides_active_promotions_for_given_subject_channel(
        $promotionRepository,
        ChannelInterface $channel,
        PromotionInterface $promotion1,
        PromotionInterface $promotion2,
        OrderInterface $subject
    ) {
        $subject->getChannel()->willReturn($channel);
        $promotionRepository->findActiveByChannel($channel)->willReturn([$promotion1, $promotion2]);

        $this->getPromotions($subject)->shouldReturn([$promotion1, $promotion2]);
    }

    function it_throws_exception_if_order_has_no_channel(OrderInterface $subject)
    {
        $subject->getChannel()->willReturn(null);

        $this
            ->shouldThrow(new \InvalidArgumentException('Order has no channel, but it should.'))
            ->during('getPromotions', [$subject])
        ;
    }

    function it_throws_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject)
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('getPromotions', [$subject]);
    }
}
