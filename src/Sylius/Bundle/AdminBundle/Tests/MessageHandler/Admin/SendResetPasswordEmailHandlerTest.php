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

namespace Sylius\Bundle\AdminBundle\Tests\MessageHandler\Admin;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account\SendResetPasswordEmailHandler;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendResetPasswordEmailHandlerTest extends KernelTestCase
{
    /** @test */
    public function it_sends_password_reset_token_email(): void
    {
        $container = self::bootKernel()->getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var SenderInterface $emailSender */
        $emailSender = $container->get('sylius.email_sender');

        $adminUser = new AdminUser();
        $adminUser->setEmail('sylius@example.com');
        $adminUser->setPasswordResetToken('my_reset_token');

        $adminUserRepository = $this->createMock(UserRepositoryInterface::class);
        $adminUserRepository
            ->method('findOneByEmail')
            ->with('sylius@example.com')
            ->willReturn($adminUser)
        ;

        $resetPasswordEmailHandler = new SendResetPasswordEmailHandler($adminUserRepository, $emailSender);
        $resetPasswordEmailHandler(new SendResetPasswordEmail(
            'sylius@example.com',
            'en_US',
        ));

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'sylius@example.com');
        self::assertEmailHtmlBodyContains(
            $email,
            $translator->trans('sylius.email.admin_password_reset.to_reset_your_password', [], null, 'en_US'),
        );
    }
}
