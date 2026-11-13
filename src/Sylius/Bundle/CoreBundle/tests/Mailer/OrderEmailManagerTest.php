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

namespace Tests\Sylius\Bundle\CoreBundle\Mailer;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AllowMockObjectsWithoutExpectations]
final class OrderEmailManagerTest extends KernelTestCase
{
    private const RECIPIENT_EMAIL = 'test@example.com';

    private const LOCALE_CODE = 'en_US';

    private const ORDER_NUMBER = '#000001';

    #[Test]
    public function it_sends_order_confirmation_email(): void
    {
        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var OrderEmailManagerInterface $orderEmailManager */
        $orderEmailManager = $container->get('sylius.mailer.order_email_manager');
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface&MockObject $customer */
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getEmail')->willReturn(self::RECIPIENT_EMAIL);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $order->method('getCustomer')->willReturn($customer);
        $order->method('getChannel')->willReturn($channel);
        $order->method('getLocaleCode')->willReturn(self::LOCALE_CODE);
        $order->method('getNumber')->willReturn(self::ORDER_NUMBER);
        $order->method('getTokenValue')->willReturn('ASFAFA4654AF');

        $orderEmailManager->sendConfirmationEmail($order);

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', self::RECIPIENT_EMAIL);
        self::assertStringContainsString(
            sprintf(
                '%s %s %s',
                $translator->trans('sylius.email.order_confirmation.your_order_number', [], null, self::LOCALE_CODE),
                self::ORDER_NUMBER,
                $translator->trans('sylius.email.order_confirmation.has_been_successfully_placed', [], null, self::LOCALE_CODE),
            ),
            preg_replace('/\s+/', ' ', strip_tags($email->getHtmlBody())),
        );
    }
}
