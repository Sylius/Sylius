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

namespace spec\Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    public function let(
        UserRepositoryInterface $userRepository,
        SenderInterface $sender,
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
        $userRepository->findOneByEmail('admin@example.com')->willReturn($adminUser);

        $sender->send(
            Emails::ADMIN_PASSWORD_RESET,
            ['admin@example.com'],
            [
                'adminUser' => $adminUser,
                'localeCode' => 'en_US',
            ],
        )->shouldBeCalledOnce();

        $this(new SendResetPasswordEmail('admin@example.com', 'en_US'));
    }

    public function it_throws_exception_while_handling_if_user_doesnt_exist(
        UserRepositoryInterface $userRepository,
    ): void {
        $userRepository->findOneByEmail('admin@example.com')->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new SendResetPasswordEmail('admin@example.com', 'en_US')])
        ;
    }
}
