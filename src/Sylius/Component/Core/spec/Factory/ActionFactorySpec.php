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
use Sylius\Component\Core\Factory\ActionFactory;
use Sylius\Component\Core\Factory\ActionFactoryInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin ActionFactory
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ActionFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory)
    {
        $this->beConstructedWith($decoratedFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ActionFactory::class);
    }

    function it_implements_action_factory_interface()
    {
        $this->shouldImplement(ActionFactoryInterface::class);
    }

    function it_creates_new_action_with_default_action_factory(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $this->createNew()->shouldReturn($action);
    }

    function it_creates_new_fixed_discount_action_with_given_amount(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(FixedDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $action->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createFixedDiscount(1000)->shouldReturn($action);
    }

    function it_creates_unit_fixed_discount_action_with_given_amount(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(UnitFixedDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $action->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createUnitFixedDiscount(1000)->shouldReturn($action);
    }

    function it_creates_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(PercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createPercentageDiscount(0.1)->shouldReturn($action);
    }

    function it_creates_unit_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(UnitPercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createUnitPercentageDiscount(0.1)->shouldReturn($action);
    }

    function it_creates_shipping_percentage_discount_action_with_given_discount_rate(
        FactoryInterface $decoratedFactory,
        ActionInterface $action
    ) {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(ShippingPercentageDiscountPromotionActionCommand::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createShippingPercentageDiscount(0.1)->shouldReturn($action);
    }
}
