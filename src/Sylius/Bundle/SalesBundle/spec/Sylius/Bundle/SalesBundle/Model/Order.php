<?php

namespace spec\Sylius\Bundle\SalesBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Order model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Order extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Model\Order');
    }

    function it_should_be_a_Sylius_order()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderInterface');
    }

    function it_should_be_a_Sylius_sales_adjustable()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustableInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_intitialize_items_collection_by_default()
    {
        $this->getItems()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
    }

    /**
     * @param Doctrine\Common\Collections\Collection $items
     */
    function its_items_collection_should_be_mutable($items)
    {
        $this->setItems($items);
        $this->getItems()->shouldReturn($items);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_should_have_fluid_interface_for_items_management($item)
    {
        $this->addItem($item)->shouldReturn($this);
        $this->removeItem($item)->shouldReturn($this);

        $this->clearItems()->shouldReturn($this);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_should_add_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_should_remove_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);
    }

    function it_should_have_items_total_equal_to_0_by_default()
    {
        $this->getItemsTotal()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item3
     */
    function it_should_calculate_correct_items_total($item1, $item2, $item3)
    {
        $item1->getTotal()->willReturn(299.99);
        $item2->getTotal()->willReturn(450);
        $item3->getTotal()->willReturn(2.50);

        $item1->equals(ANY_ARGUMENT)->willReturn(false);
        $item2->equals(ANY_ARGUMENT)->willReturn(false);
        $item3->equals(ANY_ARGUMENT)->willReturn(false);

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addItem($item3)
        ;

        $this->calculateItemsTotal();

        $this->getItemsTotal()->shouldReturn(752.49);
    }

    function it_should_initialize_adjustments_collection_by_default()
    {
        $this->getAdjustments()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_add_adjustments_properly($adjustment)
    {
        $adjustment->setAdjustable($this)->shouldBeCalled();

        $this->hasAdjustment($adjustment)->shouldReturn(false);
        $this->addAdjustment($adjustment);
        $this->hasAdjustment($adjustment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_remove_adjustments_properly($adjustment)
    {
        $this->hasAdjustment($adjustment)->shouldReturn(false);

        $adjustment->setAdjustable($this)->shouldBeCalled();
        $this->addAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(true);

        $adjustment->setAdjustable(null)->shouldBeCalled();
        $this->removeAdjustment($adjustment);

        $this->hasAdjustment($adjustment)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment
     */
    function it_should_have_fluid_interface_for_adjustments_management($adjustment)
    {
        $this->addAdjustment($adjustment)->shouldReturn($this);
        $this->removeAdjustment($adjustment)->shouldReturn($this);
    }

    function it_should_have_adjustments_total_equal_to_0_by_default()
    {
        $this->getAdjustmentsTotal()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment1
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment2
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment3
     */
    function it_should_calculate_correct_adjustments_total($adjustment1, $adjustment2, $adjustment3)
    {
        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(-49.99);
        $adjustment3->getAmount()->willReturn(19.29);

        $this
            ->addAdjustment($adjustment1)
            ->addAdjustment($adjustment2)
            ->addAdjustment($adjustment3)
        ;

        $this->calculateAdjustmentsTotal();

        $this->getAdjustmentsTotal()->shouldReturn(69.30);
    }

    function it_should_have_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment1
     * @param Sylius\Bundle\SalesBundle\Model\AdjustmentInterface $adjustment2
     */
    function it_should_calculate_correct_total($item1, $item2, $adjustment1, $adjustment2)
    {
        $item1->getTotal()->willReturn(299.99);
        $item2->getTotal()->willReturn(450);

        $item1->equals(ANY_ARGUMENT)->willReturn(false);
        $item2->equals(ANY_ARGUMENT)->willReturn(false);

        $adjustment1->getAmount()->willReturn(100);
        $adjustment2->getAmount()->willReturn(-49.99);

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addAdjustment($adjustment1)
            ->addAdjustment($adjustment2)
        ;

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(800.00);
    }

    function it_should_be_confirmed_by_default()
    {
        $this->shouldBeConfirmed();
    }

    function its_confirmation_status_should_be_mutable()
    {
        $this->setConfirmed(false);
        $this->isConfirmed()->shouldReturn(false);
    }

    function it_should_generate_confirmation_token_by_default()
    {
        $this->getConfirmationToken()->shouldBeString();
    }

    function its_confirmation_token_should_be_mutable()
    {
        $this->setConfirmationToken('abc123');
        $this->getConfirmationToken()->shouldReturn('abc123');
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
