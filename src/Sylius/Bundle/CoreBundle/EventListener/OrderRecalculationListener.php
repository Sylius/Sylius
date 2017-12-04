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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class OrderRecalculationListener
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
     *
     * @throws \InvalidArgumentException
     */
    public function recalculateOrder(GenericEvent $event): void
    {
        $order = $event->getSubject();

        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderProcessor->process($order);
    }
}
