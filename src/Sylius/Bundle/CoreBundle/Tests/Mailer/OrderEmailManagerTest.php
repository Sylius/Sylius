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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderEmailManagerTest extends KernelTestCase
{
    private const RECIPIENT_EMAIL = 'test@example.com';

    private const LOCALE_CODE = 'en_US';

    private const ORDER_NUMBER = '#000001';

    /**
     * @test
     */
    public function it_sends_order_confirmation_email(): void
    {
        static::bootKernel();

        /** @var Filesystem $filesystem */
        $filesystem = static::$kernel->getContainer()->get('filesystem');

        /** @var TranslatorInterface $translator */
        $translator = static::$kernel->getContainer()->get('translator');

        /** @var EmailChecker $emailChecker */
        $emailChecker = static::$kernel->getContainer()->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

        $orderEmailManager = static::$kernel->getContainer()->get('sylius.mailer.order_email_manager');
        /** @var OrderInterface|ObjectProphecy $order */
        $order = $this->prophesize(OrderInterface::class);
        /** @var CustomerInterface|ObjectProphecy $customer */
        $customer = $this->prophesize(CustomerInterface::class);
        $customer->getEmail()->willReturn(self::RECIPIENT_EMAIL);
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);

        $order->getCustomer()->willReturn($customer->reveal());
        $order->getChannel()->willReturn($channel->reveal());
        $order->getLocaleCode()->willReturn(self::LOCALE_CODE);
        $order->getNumber()->willReturn(self::ORDER_NUMBER);
        $order->getTokenValue()->willReturn('ASFAFA4654AF');

        $orderEmailManager->sendConfirmationEmail($order->reveal());

        $this->assertSame(1, $emailChecker->countMessagesTo(self::RECIPIENT_EMAIL));
        $this->assertTrue($emailChecker->hasMessageTo(
            sprintf(
                '%s %s %s',
                $translator->trans('sylius.email.order_confirmation.your_order_number', [], null, self::LOCALE_CODE),
                self::ORDER_NUMBER,
                $translator->trans('sylius.email.order_confirmation.has_been_successfully_placed', [], null, self::LOCALE_CODE),
            ),
            self::RECIPIENT_EMAIL,
        ));
    }
}
