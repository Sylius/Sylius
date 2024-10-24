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

namespace spec\Sylius\Component\Promotion\Checker\Eligibility;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionArchivalEligibilityCheckerSpec extends ObjectBehavior
{
    function it_is_a_promotion_eligibility_checker(): void
    {
        $this->shouldImplement(PromotionEligibilityCheckerInterface::class);
    }

    function it_is_eligible_when_archived_at_is_null(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion,
    ): void {
        $promotion->getArchivedAt()->willReturn(null);

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(true);
    }

    function it_is_not_eligible_when_archived_at_is_not_null(
        PromotionSubjectInterface $promotionSubject,
        PromotionInterface $promotion,
    ): void {
        $promotion->getArchivedAt()->willReturn(new \DateTime());

        $this->isEligible($promotionSubject, $promotion)->shouldReturn(false);
    }
}
