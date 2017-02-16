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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ShopBundle\EventListener\OrderPromotionIntegrityChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderPromotionIntegrityCheckerSpec extends ObjectBehavior
{
    function let(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        EventDispatcherInterface $dispatcher,
        RouterInterface $router
    ) {
        $this->beConstructedWith($promotionEligibilityChecker, $dispatcher, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPromotionIntegrityChecker::class);
    }

    function it_does_nothing_if_given_order_has_valid_promotion_applied(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        EventDispatcherInterface $dispatcher,
        OrderInterface $order,
        PromotionInterface $promotion,
        ResourceControllerEvent $event
    ) {
        $event->getSubject()->willReturn($order);
        $order->getPromotions()->willReturn([$promotion]);
        $promotionEligibilityChecker->isEligible($order, $promotion)->willReturn(true);
        $event->stop(Argument::any())->shouldNotBeCalled();
        $event->setResponse(Argument::any())->shouldNotBeCalled();
        $dispatcher->dispatch(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->check($event);
    }

    function it_stops_future_action_if_given_order_has_invalid_promotion_applied(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        RouterInterface $router,
        OrderInterface $order,
        PromotionInterface $promotion,
        ResourceControllerEvent $event
    ) {
        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout.com');

        $promotion->getName()->willReturn('Christmas');
        $event->getSubject()->willReturn($order);
        $order->getPromotions()->willReturn([$promotion]);
        $promotionEligibilityChecker->isEligible($order, $promotion)->willReturn(false);

        $event->stop(
            'sylius.order.promotion_integrity',
            ResourceControllerEvent::TYPE_ERROR,
            ['%promotionName%' => 'Christmas']
        )->shouldBeCalled();

        $event->setResponse(new RedirectResponse('checkout.com'))->shouldBeCalled();

        $this->check($event);
    }

    function it_throws_invalid_argument_exception_if_event_subject_is_not_order(ResourceControllerEvent $event)
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('check', [$event]);
    }
}
