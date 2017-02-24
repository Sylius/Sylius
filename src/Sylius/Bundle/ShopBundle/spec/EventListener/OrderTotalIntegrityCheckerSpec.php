<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ShopBundle\EventListener\OrderTotalIntegrityChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderTotalIntegrityCheckerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor, RouterInterface $router, ObjectManager $manager)
    {
        $this->beConstructedWith($orderProcessor, $router, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderTotalIntegrityChecker::class);
    }

    function it_does_nothing_if_prices_do_not_change(
        OrderProcessorInterface $orderProcessor,
        OrderInterface $order,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($order);

        $orderProcessor->process($order)->shouldBeCalled();

        $order->getTotal()->willReturn(1000);
        $order->getTotal()->willReturn(1000);

        $event->stop(Argument::any())->shouldNotBeCalled();
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $this->check($event);
    }

    function it_stops_process_when_it_detects_any_difference_in_order_total(
        OrderProcessorInterface $orderProcessor,
        RouterInterface $router,
        ObjectManager $manager,
        OrderInterface $order,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($order);

        $order->getTotal()->willReturn(1000, 1500);

        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout-complete.com');

        $orderProcessor->process($order)->shouldBeCalled();
        $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR)->shouldBeCalled();
        $event->setResponse(new RedirectResponse('checkout-complete.com'))->shouldBeCalled();
        $manager->persist($order)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->check($event);
    }

    function it_throws_invalid_argument_exception_if_subject_it_not_order(ResourceControllerEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('check', [$event]);
    }
}
