<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\OrderTotalIntegrityChecker;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderTotalIntegrityCheckerSpec extends ObjectBehavior
{
    function let(OrderProcessorInterface $orderProcessor, RouterInterface $router)
    {
        $this->beConstructedWith($orderProcessor, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderTotalIntegrityChecker::class);
    }

    function it_does_nothing_if_prices_do_not_change(
        OrderProcessorInterface $orderProcessor,
        OrderInterface $originalOrder,
        OrderInterface $copiedOrder,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($originalOrder);
        $originalOrder->getCopy()->willReturn($copiedOrder);

        $originalOrder->getTotal()->willReturn(1000);
        $copiedOrder->getTotal()->willReturn(1000);

        $orderProcessor->process($copiedOrder)->shouldBeCalled();
        $event->stop(Argument::any())->shouldNotBeCalled();
        $event->setResponse(Argument::any())->shouldNotBeCalled();
        $orderProcessor->process($originalOrder)->shouldNotBeCalled();

        $this->check($event);
    }

    function it_stops_process_when_it_detects_any_difference_in_order_total(
        OrderProcessorInterface $orderProcessor,
        RouterInterface $router,
        OrderInterface $originalOrder,
        OrderInterface $copiedOrder,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($originalOrder);
        $originalOrder->getCopy()->willReturn($copiedOrder);

        $originalOrder->getTotal()->willReturn(1000);
        $copiedOrder->getTotal()->willReturn(1500);

        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout-complete.com');

        $orderProcessor->process($copiedOrder)->shouldBeCalled();
        $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR)->shouldBeCalled();
        $event->setResponse(new RedirectResponse('checkout-complete.com'))->shouldBeCalled();

        $this->check($event);
    }

    function it_throws_invalid_argument_exception_if_subject_it_not_order(ResourceControllerEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('check', [$event]);
    }
}
