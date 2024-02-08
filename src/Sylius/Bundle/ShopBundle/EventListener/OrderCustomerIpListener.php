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

use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class OrderCustomerIpListener
{
    public function __construct(private IpAssignerInterface $ipAssigner, private RequestStack $requestStack)
    {
    }

    public function assignCustomerIpToOrder(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, OrderInterface::class);

        $request = $this->requestStack->getMainRequest();
        Assert::notNull($request);

        $this->ipAssigner->assign($subject, $request);
    }
}
