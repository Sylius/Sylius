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

use Sylius\Component\Core\Model\OrderItem;
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
     * @param OrderProcessorInterface $orderProcessor
     */
    public function __construct(OrderProcessorInterface $orderProcessor)
    {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * @param GenericEvent $event
     */
    public function cartItemResolver(GenericEvent $event)
    {
        $item = $event->getSubject();
        Assert::isInstanceOf($item, OrderItem::class);

        $this->orderProcessor->process($item->getOrder());
    }
}
