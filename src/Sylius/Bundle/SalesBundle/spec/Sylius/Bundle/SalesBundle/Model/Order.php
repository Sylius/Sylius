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

    function it_should_be_Sylius_order()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderInterface');
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

    function it_should_have_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_should_be_empty_by_default()
    {
        $this->countItems()->shouldReturn(0);
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
        $this->getConfirmationToken()->shouldBeString('string');
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
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item1
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item2
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item3
     */
    function it_should_calculate_correct_total($item1, $item2, $item3)
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

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(752.49);
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_should_add_and_remove_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);
    }
}
