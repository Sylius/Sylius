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

namespace spec\Sylius\Component\Promotion\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

final class ActivePromotionsProviderSpec extends ObjectBehavior
{
    function let(PromotionRepositoryInterface $promotionRepository): void
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_implements_active_promotions_provider_interface(): void
    {
        $this->shouldImplement(PreQualifiedPromotionsProviderInterface::class);
    }

    function it_provides_active_promotions(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion1,
        PromotionInterface $promotion2,
        PromotionSubjectInterface $subject
    ): void {
        $promotionRepository->findActive()->willReturn([$promotion1, $promotion2]);

        $this->getPromotions($subject)->shouldReturn([$promotion1, $promotion2]);
    }
}
