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

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\EmailChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendOrderConfirmationEmailHandlerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_sends_order_confirmation_email(): void
    {
        $container = self::bootKernel()->getContainer();

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('filesystem');

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var EmailChecker $emailChecker */
        $emailChecker = $container->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

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

        self::assertSame($emailChecker->countMessagesTo('johnny.bravo@email.com'), 1);
        self::assertTrue($emailChecker->hasMessageTo(
            sprintf(
                '%s %s %s',
                $translator->trans('sylius.email.order_confirmation.your_order_number', [], null, 'pl_PL'),
                '#000001',
                $translator->trans('sylius.email.order_confirmation.has_been_successfully_placed', [], null, 'pl_PL'),
            ),
            'johnny.bravo@email.com',
        ));
    }
}
