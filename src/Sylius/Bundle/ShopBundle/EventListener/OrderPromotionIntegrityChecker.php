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

use Doctrine\Common\Collections\ArrayCollection;
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
     * @param PromotionApplicatorInterface $promotionApplicator
     * @param EventDispatcherInterface $eventDispatcher
     * @param RouterInterface $router
     */
    public function __construct(
        PromotionEligibilityCheckerInterface $promotionEligibilityChecker,
        PromotionApplicatorInterface $promotionApplicator,
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router
    ) {
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
        $this->promotionApplicator = $promotionApplicator;
        $this->eventDispatcher = $eventDispatcher;
        $this->router = $router;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function check(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        // we creae a new promotion collection and remove them from cart
        // so we can verify with original conditions (without the price beeng applied befor check)

        $promotions = new ArrayCollection($order->getPromotions()->toArray());


        foreach($promotions as $promotion){
            $this->promotionApplicator->revert($order, $promotion);
            $order->removePromotion($promotion);
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

            $this->promotionApplicator->apply($order, $promotion);
        }
    }
}
