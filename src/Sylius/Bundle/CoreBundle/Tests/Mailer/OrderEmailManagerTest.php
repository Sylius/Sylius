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

namespace Sylius\Bundle\CoreBundle\Tests\Mailer;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Test\Services\EmailChecker;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderEmailManagerTest extends KernelTestCase
{
    public function testMailIsSentAndContentIsOk()
    {
        static::bootKernel();

        /** @var Filesystem $filesystem */
        $filesystem = static::$kernel->getContainer()->get('filesystem');
        /** @var EmailChecker $emailChecker */
        $emailChecker = static::$kernel->getContainer()->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

        $orderEmailManager = static::$kernel->getContainer()->get('sylius.mailer.order_email_manager');
        /** @var OrderInterface|ObjectProphecy $order */
        $order = $this->prophesize(OrderInterface::class);
        /** @var CustomerInterface|ObjectProphecy $customer */
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('test@example.com');
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);

        $order->getCustomer()->willReturn($customer->reveal());
        $order->getChannel()->willReturn($channel->reveal());
        $order->getLocaleCode()->willReturn('en_US');
        $order->getNumber()->willReturn('#000001');
        $order->getTokenValue()->willReturn('ASFAFA4654AF');

        $orderEmailManager->sendConfirmationEmail($order->reveal());

        $this->assertSame(1, $emailChecker->countMessagesTo('test@example.com'));
    }
}
