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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendResetPasswordEmailHandlerTest extends KernelTestCase
{
    private EmailCheckerInterface $emailChecker;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var Filesystem $filesystem */
        $filesystem = $this->getContainer()->get('filesystem');

        $this->emailChecker = $this->getContainer()->get('sylius.behat.email_checker');

        $filesystem->remove($this->emailChecker->getSpoolDirectory());
    }

    /** @test */
    public function it_sends_password_reset_token_email(): void
    {
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

        self::assertSame(1, $this->emailChecker->countMessagesTo('sylius@example.com'));
        self::assertTrue($this->emailChecker->hasMessageTo(
            $translator->trans('sylius.email.admin_password_reset.to_reset_your_password_token', [], null, 'en_US'),
            'sylius@example.com',
        ));
    }
}
