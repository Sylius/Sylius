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
use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\ShippingDiscountAction;
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
        $this->shouldHaveType('Sylius\Component\Core\Factory\ActionFactory');
    }

    function it_implements_action_factory_interface()
    {
        $this->shouldImplement(ActionFactoryInterface::class);
    }

    function it_creates_new_action_with_default_action_factory($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $this->createNew()->shouldReturn($action);
    }

    function it_creates_new_fixed_discount_action_with_given_amount($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(FixedDiscountAction::TYPE)->shouldBeCalled();
        $action->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createFixedDiscount(1000)->shouldReturn($action);
    }

    function it_creates_unit_fixed_discount_action_with_given_amount($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(UnitFixedDiscountAction::TYPE)->shouldBeCalled();
        $action->setConfiguration(['amount' => 1000])->shouldBeCalled();

        $this->createUnitFixedDiscount(1000)->shouldReturn($action);
    }

    function it_creates_percentage_discount_action_with_given_discount_rate($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(PercentageDiscountAction::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createPercentageDiscount(0.1)->shouldReturn($action);
    }

    function it_creates_unit_percentage_discount_action_with_given_discount_rate($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(UnitPercentageDiscountAction::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createUnitPercentageDiscount(0.1)->shouldReturn($action);
    }

    function it_creates_shipping_discount_action_with_given_discount_rate($decoratedFactory, ActionInterface $action)
    {
        $decoratedFactory->createNew()->willReturn($action);

        $action->setType(ShippingDiscountAction::TYPE)->shouldBeCalled();
        $action->setConfiguration(['percentage' => 0.1])->shouldBeCalled();

        $this->createPercentageShippingDiscount(0.1)->shouldReturn($action);
    }
}
