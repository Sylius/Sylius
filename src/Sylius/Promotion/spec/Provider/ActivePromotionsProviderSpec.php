<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Promotion\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActivePromotionsProviderSpec extends ObjectBehavior
{
    function let(PromotionRepositoryInterface $promotionRepository)
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Promotion\Provider\ActivePromotionsProvider');
    }

    function it_implements_active_promotions_provider_interface()
    {
        $this->shouldImplement(PreQualifiedPromotionsProviderInterface::class);
    }

    function it_provides_active_promotions(
        $promotionRepository,
        PromotionInterface $promotion1,
        PromotionInterface $promotion2,
        PromotionSubjectInterface $subject
    ) {
        $promotionRepository->findActive()->willReturn([$promotion1, $promotion2]);

        $this->getPromotions($subject)->shouldReturn([$promotion1, $promotion2]);
    }
}
