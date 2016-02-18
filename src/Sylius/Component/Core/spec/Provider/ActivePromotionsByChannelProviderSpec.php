<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

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
        $this->shouldHaveType('Sylius\Component\Core\Provider\ActivePromotionsByChannelProvider');
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

    function it_throws_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $subject)
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('getPromotions', [$subject]);
    }
}
