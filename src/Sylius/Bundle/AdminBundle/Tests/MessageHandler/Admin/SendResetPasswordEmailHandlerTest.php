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
use Sylius\Component\Core\Test\SwiftmailerAssertionTrait;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendResetPasswordEmailHandlerTest extends KernelTestCase
{
    use SwiftmailerAssertionTrait;

    /** @test */
    public function it_sends_password_reset_token_email(): void
    {
        if (self::isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment without swiftmailer');
        }

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

    /** @test */
    public function it_sends_password_reset_token_email_with_swiftmailer(): void
    {
        if (!self::isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment with swiftmailer');
        }

        $container = self::bootKernel()->getContainer();

        self::setSpoolDirectory($container->getParameter('kernel.cache_dir') . '/spool');

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('test.filesystem.public');

        $filesystem->remove(self::getSpoolDirectory());

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

        self::assertSpooledMessagesCountWithRecipient(1, 'sylius@example.com');
        self::assertSpooledMessageWithContentHasRecipient(
            $translator->trans('sylius.email.admin_password_reset.to_reset_your_password', [], null, 'en_US'),
            'sylius@example.com',
        );
    }

    private static function isItSwiftmailerTestEnv(): bool
    {
        $env = self::getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
