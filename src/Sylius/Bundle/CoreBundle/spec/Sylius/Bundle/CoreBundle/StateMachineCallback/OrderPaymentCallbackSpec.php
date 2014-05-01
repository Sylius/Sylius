<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Callback;

use Finite\Factory\FactoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;
use Sylius\Component\Core\SyliusOrderEvents;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class OrderPaymentCallbackSpec extends ObjectBehavior
{
    function let(EntityRepository $repository, FactoryInterface $factory)
    {
        $this->beConstructedWith($repository, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Callback\OrderPaymentCallback');
    }

    function it_dispatches_event_on_payment_if_complete(
        $factory,
        $repository,
        PaymentInterface $payment,
        OrderInterface $order,
        StateMachineInterface $sm
    ) {
        $repository->findOneBy(array('payment' => $payment))->willReturn($order);

        $factory->get($order, OrderTransitions::GRAPH)->willReturn($sm);
        $sm->apply(OrderTransitions::SYLIUS_CONFIRM)->shouldBeCalled();

        $this->updateOrderOnPayment($payment);
    }
}
