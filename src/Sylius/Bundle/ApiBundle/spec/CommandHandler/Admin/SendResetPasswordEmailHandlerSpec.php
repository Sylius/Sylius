<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Admin;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Admin\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    private const SAMPLE_EMAIL = 'admin@example.com';

    private const SAMPLE_LOCALE_CODE = 'en_US';

    public function let(
        UserRepositoryInterface $userRepository,
        SenderInterface $sender
    ): void {
        $this->beConstructedWith($userRepository, $sender);
    }

    public function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_handles_sending_reset_password_email(
        UserRepositoryInterface $userRepository,
        SenderInterface $sender,
        AdminUserInterface $adminUser,
    ): void {
        $userRepository->findOneByEmail(self::SAMPLE_EMAIL)->willReturn($adminUser);

        $sender->send(
            Emails::ADMIN_PASSWORD_RESET,
            [self::SAMPLE_EMAIL],
            [
                'adminUser' => $adminUser,
                'localeCode' => self::SAMPLE_LOCALE_CODE,
            ]
        )->shouldBeCalledOnce();

        $sendResetPasswordEmail = new SendResetPasswordEmail(self::SAMPLE_EMAIL, self::SAMPLE_LOCALE_CODE);
        $this->__invoke($sendResetPasswordEmail);
    }

    public function it_throws_exception_while_handling_if_user_doesnt_exist(
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail(self::SAMPLE_EMAIL)->willReturn(null);

        $sendResetPasswordEmail = new SendResetPasswordEmail(self::SAMPLE_EMAIL, self::SAMPLE_LOCALE_CODE);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$sendResetPasswordEmail]);
    }
}
