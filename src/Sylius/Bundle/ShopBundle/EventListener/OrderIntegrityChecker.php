<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderIntegrityChecker implements OrderIntegrityCheckerInterface
{
    public function __construct(
        private RouterInterface $router,
        private ObjectManager $manager,
        private OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ) {
    }

    public function check(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $oldTotal = $order->getTotal();

        if ($promotion = $this->orderPromotionsIntegrityChecker->check($order)) {
            $event->stop(
                'sylius.order.promotion_integrity',
                ResourceControllerEvent::TYPE_ERROR,
                ['%promotionName%' => $promotion->getName()],
            );

            $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

            $this->manager->persist($order);
            $this->manager->flush();

            return;
        }

        if ($order->getTotal() !== $oldTotal) {
            $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR);
            $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

            $this->manager->persist($order);
            $this->manager->flush();
        }
    }
}
