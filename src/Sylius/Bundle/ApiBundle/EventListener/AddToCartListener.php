<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class AddToCartListener
{
    /**
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param OrderProcessorInterface $orderProcessor
     * @param ObjectManager $objectManager
     */
    public function __construct(OrderProcessorInterface $orderProcessor, ObjectManager $objectManager)
    {
        $this->orderProcessor = $orderProcessor;
        $this->objectManager = $objectManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function recalculateOrder(GenericEvent $event)
    {
        $item = $event->getSubject();
        Assert::isInstanceOf($item, OrderItemInterface::class);
        $order = $item->getOrder();

        $this->orderProcessor->process($order);

        $this->objectManager->persist($order);
    }
}
