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

namespace Sylius\Bundle\ApiBundle\Tests\MessageHandler\Admin;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account\SendResetPasswordEmailHandler;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Test\Services\EmailCheckerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendResetPasswordEmailHandlerTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_password_reset_token_email(): void
    {
        if ($this->isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment without swiftmailer');
        }

        /** @var TranslatorInterface $translator */
        $translator = $this->getContainer()->get('translator');

        /** @var SenderInterface $emailSender */
        $emailSender = $this->getContainer()->get('sylius.email_sender');

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

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'To', 'sylius@example.com');
        $this->assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.admin_password_reset.to_reset_your_password_token', [], null, 'en_US'));
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = $this->getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
