<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class AdjustmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Model\Adjustment');
    }

    function it_implements_Sylius_adjustment_interface()
    {
        $this->shouldImplement(AdjustmentInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_an_adjustable_by_default()
    {
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_an_adjustable(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);
    }

    function it_allows_detaching_itself_from_an_adjustable(OrderInterface $order, OrderItemInterface $orderItem)
    {
        $this->setAdjustable($order);
        $this->getAdjustable()->shouldReturn($order);

        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);

        $this->setAdjustable($orderItem);
        $this->getAdjustable()->shouldReturn($orderItem);

        $this->setAdjustable(null);
        $this->getAdjustable()->shouldReturn(null);
    }

    function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable()
    {
        $this->setType('some type');
        $this->getType()->shouldReturn('some type');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('Clothing tax (12%)');
        $this->getDescription()->shouldReturn('Clothing tax (12%)');
    }

    function it_has_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable()
    {
        $this->setAmount(399);
        $this->getAmount()->shouldReturn(399);
    }

    function its_amount_should_accept_only_integer()
    {
        $this->setAmount(4498);
        $this->getAmount()->shouldBeInteger();
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetAmount(44.98 * 100);
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetAmount('4498');
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetAmount(round(44.98 * 100));
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetAmount(array(4498));
        $this->shouldThrow(\InvalidArgumentException::class)->duringSetAmount(new \stdClass());
    }

    function it_is_not_neutral_by_default()
    {
        $this->shouldNotBeNeutral();
    }

    function its_neutrality_is_mutable()
    {
        $this->shouldNotBeNeutral();
        $this->setNeutral(true);
        $this->shouldBeNeutral();
    }

    function it_is_a_charge_if_amount_is_lesser_than_0()
    {
        $this->setAmount(-499);
        $this->shouldBeCharge();

        $this->setAmount(699);
        $this->shouldNotBeCharge();
    }

    function it_is_a_credit_if_amount_is_greater_than_0()
    {
        $this->setAmount(2999);
        $this->shouldBeCredit();

        $this->setAmount(-299);
        $this->shouldNotBeCredit();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
