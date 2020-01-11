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

namespace Sylius\Bundle\AdminApiBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CartChangeListener
{
    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ObjectManager */
    private $objectManager;

    public function __construct(OrderProcessorInterface $orderProcessor, ObjectManager $objectManager)
    {
        $this->orderProcessor = $orderProcessor;
        $this->objectManager = $objectManager;
    }

    public function recalculateOrderOnAdd(GenericEvent $event): void
    {
        $item = $event->getSubject();
        Assert::isInstanceOf($item, OrderItemInterface::class);
        $order = $item->getOrder();

        $this->orderProcessor->process($order);

        $this->objectManager->persist($order);
    }

    public function recalculateOrderOnDelete(GenericEvent $event): void
    {
        $item = $event->getSubject();
        Assert::isInstanceOf($item, OrderItemInterface::class);

        /** @var OrderInterface $order */
        $order = $item->getOrder();
        $order->removeItem($item);

        $this->orderProcessor->process($order);
    }
}
