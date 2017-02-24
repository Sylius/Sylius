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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderTotalIntegrityChecker
{
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessors;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @param OrderProcessorInterface $orderProcessors
     * @param RouterInterface $router
     * @param ObjectManager $manager
     */
    public function __construct(
        OrderProcessorInterface $orderProcessors,
        RouterInterface $router,
        ObjectManager $manager
    ) {
        $this->orderProcessors = $orderProcessors;
        $this->router = $router;
        $this->manager = $manager;
    }

    /**
     * @param ResourceControllerEvent $event
     */
    public function check(ResourceControllerEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        $oldTotal = $order->getTotal();
        $this->orderProcessors->process($order);

        if ($order->getTotal() !== $oldTotal) {
            $event->stop('sylius.order.total_integrity', ResourceControllerEvent::TYPE_ERROR);
            $event->setResponse(new RedirectResponse($this->router->generate('sylius_shop_checkout_complete')));

            $this->manager->persist($order);
            $this->manager->flush();
        }
    }
}
