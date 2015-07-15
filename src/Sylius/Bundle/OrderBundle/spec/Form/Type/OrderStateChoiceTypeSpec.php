<?php

namespace spec\Sylius\Bundle\OrderBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderStateChoiceTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\OrderStateChoiceType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_has_option(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
                OrderInterface::STATE_CART        => 'sylius.order.state.checkout',
                OrderInterface::STATE_CART_LOCKED => 'sylius.order.state.cart_locked',
                OrderInterface::STATE_PENDING     => 'sylius.order.state.ordered',
                OrderInterface::STATE_CONFIRMED   => 'sylius.order.state.order_confimed',
                OrderInterface::STATE_SHIPPED     => 'sylius.order.state.shipped',
                OrderInterface::STATE_ABANDONED   => 'sylius.order.state.abandoned',
                OrderInterface::STATE_CANCELLED   => 'sylius.order.state.cancelled',
                OrderInterface::STATE_RETURNED    => 'sylius.order.state.returned',
            )
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_order_state_choice');
    }
}
