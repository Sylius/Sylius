<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

final class OrderIntegrityCheckerSpec extends ObjectBehavior
{
    function let(
        RouterInterface $router,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $orderManager
    ): void {
        $this->beConstructedWith($router, $orderProcessor, $orderManager);
    }

    function it_does_nothing_if_given_order_has_valid_promotion_applied(
        OrderProcessorInterface $orderProcessor,
        OrderInterface $order,
        PromotionInterface $promotion,
        ResourceControllerEvent $event
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getPromotions()->willReturn(new ArrayCollection([$promotion->getWrappedObject()]));
        $order->getTotal()->willReturn(1000);

        $orderProcessor->process($order)->shouldBeCalled();

        $event->stop(Argument::any())->shouldNotBeCalled();
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $this->check($event);
    }

    function it_stops_future_action_if_given_order_has_different_promotion_applied(
        OrderProcessorInterface $orderProcessor,
        RouterInterface $router,
        OrderInterface $order,
        PromotionInterface $oldPromotion,
        PromotionInterface $newPromotion,
        ResourceControllerEvent $event,
        ObjectManager $orderManager
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getPromotions()->willReturn(
            new ArrayCollection([$oldPromotion->getWrappedObject()]),
            new ArrayCollection([$newPromotion->getWrappedObject()])
        );
        $order->getTotal()->willReturn(1000);

        $oldPromotion->getName()->willReturn('Christmas');

        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout.com');

        $orderProcessor->process($order)->shouldBeCalled();

        $event->stop(
            'sylius.order.promotion_integrity',
            ResourceControllerEvent::TYPE_ERROR,
            ['%promotionName%' => 'Christmas']
        )->shouldBeCalled();
        $event->setResponse(new RedirectResponse('checkout.com'))->shouldBeCalled();

        $orderManager->persist($order)->shouldBeCalled();
        $orderManager->flush()->shouldBeCalled();

        $this->check($event);
    }

    function it_stops_future_action_if_given_order_has_different_total_value(
        OrderProcessorInterface $orderProcessor,
        RouterInterface $router,
        OrderInterface $order,
        PromotionInterface $promotion,
        ResourceControllerEvent $event,
        ObjectManager $orderManager
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getPromotions()->willReturn(new ArrayCollection([$promotion->getWrappedObject()]));
        $order->getTotal()->willReturn(1000, 1500);

        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout.com');

        $orderProcessor->process($order)->shouldBeCalled();

        $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR)->shouldBeCalled();
        $event->setResponse(new RedirectResponse('checkout.com'))->shouldBeCalled();

        $orderManager->persist($order)->shouldBeCalled();
        $orderManager->flush()->shouldBeCalled();

        $this->check($event);
    }

    function it_stops_future_action_if_given_order_has_no_promotion_applied(
        OrderProcessorInterface $orderProcessor,
        RouterInterface $router,
        OrderInterface $order,
        PromotionInterface $promotion,
        ResourceControllerEvent $event,
        ObjectManager $orderManager
    ): void {
        $event->getSubject()->willReturn($order);

        $order->getPromotions()->willReturn(
            new ArrayCollection([$promotion->getWrappedObject()]),
            new ArrayCollection([])
        );
        $order->getTotal()->willReturn(1000);

        $promotion->getName()->willReturn('Christmas');
        $promotion->getCode()->willReturn('CHRISTMAS_PROMO_CODE');

        $orderProcessor->process($order)->shouldBeCalled();

        $router->generate('sylius_shop_checkout_complete')->willReturn('checkout.com');

        $event->stop(
            'sylius.order.promotion_integrity',
            ResourceControllerEvent::TYPE_ERROR,
            ['%promotionName%' => 'Christmas']
        )->shouldBeCalled();
        $event->setResponse(new RedirectResponse('checkout.com'))->shouldBeCalled();

        $orderManager->persist($order)->shouldBeCalled();
        $orderManager->flush()->shouldBeCalled();

        $this->check($event);
    }

    function it_throws_invalid_argument_exception_if_event_subject_is_not_order(ResourceControllerEvent $event): void
    {
        $event->getSubject()->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('check', [$event]);
    }
}
