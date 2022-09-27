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
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderEmailManagerTest extends KernelTestCase
{
    private const RECIPIENT_EMAIL = 'test@example.com';

    private const LOCALE_CODE = 'en_US';

    private const ORDER_NUMBER = '#000001';

    /**
     * @test
     */
    public function it_sends_order_confirmation_email_with_symfony_mailer_if_swift_mailer_is_not_present(): void
    {
        if ($this->isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('This test should be executed only outside of test_with_swiftmailer environment');
        }

        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var OrderEmailManagerInterface $orderEmailManager */
        $orderEmailManager = $container->get('sylius.mailer.order_email_manager');
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

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', self::RECIPIENT_EMAIL);
        self::assertStringContainsString(
            sprintf(
                '%s %s %s',
                $translator->trans('sylius.email.order_confirmation.your_order_number', [], null, self::LOCALE_CODE),
                self::ORDER_NUMBER,
                $translator->trans('sylius.email.order_confirmation.has_been_successfully_placed', [], null, self::LOCALE_CODE)
            ),
            preg_replace('/\s+/', ' ', strip_tags($email->getHtmlBody()))
        );
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = self::getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
