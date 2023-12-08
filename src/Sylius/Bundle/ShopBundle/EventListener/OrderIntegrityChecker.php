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
use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderIntegrityChecker implements OrderIntegrityCheckerInterface
{
    public function __construct(
        private RouterInterface $router,
        private OrderProcessorInterface $orderProcessor,
        private ObjectManager $manager,
    ) {
    }

    public function check(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var ArrayCollection<array-key, PromotionInterface> $previousPromotions */
        $previousPromotions = new ArrayCollection($order->getPromotions()->toArray());
        $oldTotal = $order->getTotal();

        $this->orderProcessor->process($order);

        /** @var PromotionInterface $previousPromotion */
        foreach ($previousPromotions as $previousPromotion) {
            if (!$order->getPromotions()->contains($previousPromotion)) {
                $event->stop(
                    'sylius.order.promotion_integrity',
                    ResourceControllerEvent::TYPE_ERROR,
                    ['%promotionName%' => $previousPromotion->getName()],
                );

                $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

                $this->manager->persist($order);
                $this->manager->flush();

                return;
            }
        }

        if ($order->getTotal() !== $oldTotal) {
            $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR);
            $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

            $this->manager->persist($order);
            $this->manager->flush();

            return;
        }
    }
}
