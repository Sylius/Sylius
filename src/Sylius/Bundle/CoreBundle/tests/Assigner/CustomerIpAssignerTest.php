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

namespace Tests\Sylius\Bundle\CoreBundle\Assigner;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Assigner\CustomerIpAssigner;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class CustomerIpAssignerTest extends TestCase
{
    private CustomerIpAssigner $customerIpAssigner;

    protected function setUp(): void
    {
        $this->customerIpAssigner = new CustomerIpAssigner();
    }

    public function testImplementsIpAssignerInterface(): void
    {
        $this->assertInstanceOf(IpAssignerInterface::class, $this->customerIpAssigner);
    }

    public function testAssignsCustomerIpFromRequestToOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $request = $this->createMock(Request::class);
        $request->expects($this->once())->method('getClientIp')->willReturn('172.16.254.1');
        $order->expects($this->once())->method('setCustomerIp')->with('172.16.254.1')->willReturnSelf();
        $this->customerIpAssigner->assign($order, $request);
    }
}
