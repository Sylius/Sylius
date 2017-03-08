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

use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderCompleteListener
{
    /**
     * @var OrderEmailManagerInterface
     */
    private $orderEmailManager;

    /**
     * @param OrderEmailManagerInterface $orderEmailManager
     */
    public function __construct(OrderEmailManagerInterface $orderEmailManager)
    {
        $this->orderEmailManager = $orderEmailManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function sendConfirmationEmail(GenericEvent $event)
    {
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderEmailManager->sendConfirmationEmail($order);
    }
}
