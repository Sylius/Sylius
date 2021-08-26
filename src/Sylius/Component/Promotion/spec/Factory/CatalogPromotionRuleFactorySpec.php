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

namespace spec\Sylius\Component\Promotion\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Factory\CatalogPromotionRuleFactoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CatalogPromotionRuleFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_a_catalog_promotion_rule_factory_interface(): void
    {
        $this->shouldImplement(CatalogPromotionRuleFactoryInterface::class);
    }

    function it_creates_a_new_catalog_promotion_rule(FactoryInterface $factory, CatalogPromotionRuleInterface $rule): void
    {
        $factory->createNew()->willReturn($rule);

        $this->createNew()->shouldReturn($rule);
    }

    function it_creates_a_catalog_promotion_rule_with_data(
        FactoryInterface $factory,
        CatalogPromotionRuleInterface $rule,
        CatalogPromotionInterface $catalogPromotion

    ): void {
        $factory->createNew()->willReturn($rule);
        $rule->setType('rule_type')->shouldBeCalled();
        $rule->setConfiguration(['variant_code'])->shouldBeCalled();
        $rule->setCatalogPromotion($catalogPromotion)->shouldBeCalled();

        $this->createWithData('rule_type', $catalogPromotion, ['variant_code'])->shouldReturn($rule);
    }
}
