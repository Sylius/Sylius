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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class OrderIntegrityChecker
{
    /** @var RouterInterface */
    private $router;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ObjectManager */
    private $manager;

    public function __construct(
        RouterInterface $router,
        OrderProcessorInterface $orderProcessor,
        ObjectManager $manager
    ) {
        $this->router = $router;
        $this->orderProcessor = $orderProcessor;
        $this->manager = $manager;
    }

    public function check(ResourceControllerEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        $previousPromotions = new ArrayCollection($order->getPromotions()->toArray());
        $oldTotal = $order->getTotal();

        $this->orderProcessor->process($order);

        /** @var PromotionInterface $previousPromotion */
        foreach ($previousPromotions as $previousPromotion) {
            if (!$order->getPromotions()->contains($previousPromotion)) {
                $event->stop(
                    'sylius.order.promotion_integrity',
                    ResourceControllerEvent::TYPE_ERROR,
                    ['%promotionName%' => $previousPromotion->getName()]
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
