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

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

final class PromotionRuleSpec extends ObjectBehavior
{
    function it_is_a_promotion_rule(): void
    {
        $this->shouldImplement(PromotionRuleInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_have_type_by_default(): void
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable(): void
    {
        $this->setType('type');
        $this->getType()->shouldReturn('type');
    }

    function it_initializes_array_for_configuration_by_default(): void
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_is_mutable(): void
    {
        $this->setConfiguration(['value' => 500]);
        $this->getConfiguration()->shouldReturn(['value' => 500]);
    }

    function it_does_not_have_a_promotion_by_default(): void
    {
        $this->getPromotion()->shouldReturn(null);
    }

    function its_promotion_by_is_mutable(PromotionInterface $promotion): void
    {
        $this->setPromotion($promotion);
        $this->getPromotion()->shouldReturn($promotion);
    }
}
