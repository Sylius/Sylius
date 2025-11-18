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

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Mailer\AccountRegistrationEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountRegistrationEmailManagerTest extends KernelTestCase
{
    #[Test]
    public function it_sends_account_registration_email(): void
    {
        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var AccountRegistrationEmailManagerInterface $accountRegistrationEmailManager */
        $accountRegistrationEmailManager = $container->get(AccountRegistrationEmailManagerInterface::class);

        /** @var UserInterface&MockObject $user */
        $user = $this->createMock(UserInterface::class);
        $user->method('getUsername')->willReturn('username');
        $user->method('getEmail')->willReturn('customer@example.com');
        $user->method('getEmailVerificationToken')->willReturn('token');
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $accountRegistrationEmailManager->sendAccountRegistrationEmail(
            $user,
            $channel,
            'en_US',
        );

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'customer@example.com');
        self::assertEmailHtmlBodyContains(
            $email,
            $translator->trans('sylius.email.user_registration.start_shopping', [], null, 'en_US'),
        );
    }
}
