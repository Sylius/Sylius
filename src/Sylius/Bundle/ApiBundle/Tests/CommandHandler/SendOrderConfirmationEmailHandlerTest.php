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

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;

final class SendOrderConfirmationEmailHandlerTest extends KernelTestCase
{
    use ProphecyTrait;
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_order_confirmation_email(): void
    {
        $container = self::bootKernel()->getContainer();
        $emailSender = $container->get('sylius.email_sender');

        /** @var OrderInterface|ObjectProphecy $order */
        $order = $this->prophesize(OrderInterface::class);
        /** @var CustomerInterface|ObjectProphecy $customer */
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn('johnny.bravo@email.com');
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);

        $order->getCustomer()->willReturn($customer->reveal());
        $order->getChannel()->willReturn($channel->reveal());
        $order->getLocaleCode()->willReturn('pl_PL');
        $order->getNumber()->willReturn('#000001');
        $order->getTokenValue()->willReturn('TOKEN');

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);

        $orderRepository->findOneByTokenValue('TOKEN')->willReturn($order);

        $sendOrderConfirmationEmailHandler = new SendOrderConfirmationHandler(
            $emailSender,
            $orderRepository->reveal(),
        );

        $sendOrderConfirmationEmailHandler(new SendOrderConfirmation('TOKEN'));

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'To', 'johnny.bravo@email.com');
        $this->assertEmailHtmlBodyContains($email, '#000001');
    }
}
