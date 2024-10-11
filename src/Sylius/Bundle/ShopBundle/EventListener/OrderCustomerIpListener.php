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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class OrderCustomerIpListener
{
    public function __construct(
        private readonly IpAssignerInterface $ipAssigner,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function __invoke(Event|OrderInterface $event): void
    {
        $order = $this->getOrder($event);
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return;
        }

        $this->ipAssigner->assign($order, $request);
    }

    private function getOrder(Event|OrderInterface $event): OrderInterface
    {
        if ($event instanceof Event) {
            $order = $event->getSubject();
            Assert::isInstanceOf($order, OrderInterface::class);
        }

        if ($event instanceof OrderInterface) {
            $order = $event;
        }

        return $order;
    }
}
