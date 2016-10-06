<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\PromotionActionFactory;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin PromotionActionFactory
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionActionFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory)
    {
        $this->beConstructedWith($decoratedFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionActionFactory::class);
    }

    function it_implements_action_factory_interface()
    {
        $this->shouldImplement(PromotionActionFactoryInterface::class);
    }

    function it_creates_new_action_with_default_action_factory(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $this->createNew()->shouldReturn($promotionAction);
    }

    function it_creates_new_fixed_discount_action_with_given_amount(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $promotionAction->setType(FixedDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $promotionAction->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createFixedDiscount(1000)->shouldReturn($promotionAction);
    }

    function it_creates_unit_fixed_discount_action_with_given_amount(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $promotionAction->setType(UnitFixedDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $promotionAction->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createUnitFixedDiscount(1000)->shouldReturn($promotionAction);
    }

    function it_creates_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $promotionAction->setType(PercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $promotionAction->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createPercentageDiscount(0.1)->shouldReturn($promotionAction);
    }

    function it_creates_unit_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $promotionAction->setType(UnitPercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $promotionAction->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createUnitPercentageDiscount(0.1)->shouldReturn($promotionAction);
    }

    function it_creates_shipping_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        PromotionActionInterface $promotionAction
    ) {
        $decoratedFactory->createNew()->willReturn($promotionAction);

        $promotionAction->setType(ShippingPercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $promotionAction->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createShippingPercentageDiscount(0.1)->shouldReturn($promotionAction);
    }
}
