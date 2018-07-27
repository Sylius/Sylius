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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderPromotionIntegrityChecker
{
    /**
     * @var PromotionEligibilityCheckerInterface
     */
    private $promotionEligibilityChecker;

    /**
     * @var PromotionApplicatorInterface
     */
    private $promotionApplicator;

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
     * @param PromotionApplicatorInterface $promotionApplicator
     */
    public function __construct(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router,
        ?PromotionApplicatorInterface $promotionApplicator = null
    ) {
        if ($promotionApplicator === null) {
            @trigger_error("You need to supply an promotion applicator in order to work properly. In case you don't provide it, there will be valid cases that will fail due an incorrect recalculation.", \E_USER_DEPRECATED);
        }

        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
        $this->promotionApplicator = $promotionApplicator;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function check(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        // we create a new promotion collection and remove them from cart
        // so we can verify with original conditions (without the price being applied before check)

        $promotions = $order->getPromotions()->toArray();

        if ($this->promotionApplicator !== null) {
            foreach ($promotions as $promotion) {
                $this->promotionApplicator->revert($order, $promotion);
                $order->removePromotion($promotion);
            }
        }

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

            if ($this->promotionApplicator !== null) {
                $this->promotionApplicator->apply($order, $promotion);
            }
        }
    }
}
