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

use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface as DeprecatedOrderEmailManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class OrderCompleteListener
{
    public function __construct(private DeprecatedOrderEmailManagerInterface|OrderEmailManagerInterface $orderEmailManager)
    {
        if ($this->orderEmailManager instanceof DeprecatedOrderEmailManagerInterface) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '1.13',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be prohibited in Sylius 2.0. Pass an instance of %s instead.',
                DeprecatedOrderEmailManagerInterface::class,
                self::class,
                OrderEmailManagerInterface::class,
            );
        }
    }

    public function sendConfirmationEmail(GenericEvent $event): void
    {
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderEmailManager->sendConfirmationEmail($order);
    }
}
