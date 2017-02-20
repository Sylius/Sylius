<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderPromotionIntegrityChecker
{
    /**
     * @var PromotionEligibilityCheckerInterface
     */
    private $promotionEligibilityChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param PromotionEligibilityCheckerInterface $promotionEligibilityChecker
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     */
    public function __construct(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router
    ) {
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function check(ResourceControllerEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        $promotions = $order->getPromotions();
        foreach ($promotions as $promotion) {
            if (!$this->promotionEligibilityChecker->isEligible($order, $promotion)) {
                $event->stop(
                    'sylius.order.promotion_integrity',
                    ResourceControllerEvent::TYPE_ERROR,
                    ['%promotionName%' => $promotion->getName()]
                );

                $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

                $this->eventDispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($order));

                break;
            }
        }
    }
}
