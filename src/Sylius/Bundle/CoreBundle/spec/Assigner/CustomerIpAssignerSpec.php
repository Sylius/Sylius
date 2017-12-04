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

namespace spec\Sylius\Bundle\CoreBundle\Assigner;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class CustomerIpAssignerSpec extends ObjectBehavior
{
    function it_implements_ip_assigner_interface(): void
    {
        $this->shouldImplement(IpAssignerInterface::class);
    }

    function it_assigns_customer_ip_from_request_to_order(OrderInterface $order, Request $request): void
    {
        $request->getClientIp()->willReturn('172.16.254.1');
        $order->setCustomerIp('172.16.254.1')->shouldBeCalled();

        $this->assign($order, $request);
    }
}
