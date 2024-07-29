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

namespace spec\Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\SendResetPasswordEmail;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    public function let(
        UserRepositoryInterface $userRepository,
        ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
    ): void {
        $this->beConstructedWith($userRepository, $resetPasswordEmailManager);
    }

    public function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_handles_sending_reset_password_email(
        UserRepositoryInterface $userRepository,
        ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
        AdminUserInterface $adminUser,
    ): void {
        $userRepository->findOneByEmail('admin@example.com')->willReturn($adminUser);

        $resetPasswordEmailManager->sendResetPasswordEmail($adminUser, 'en_US')->shouldNotBeCalled();
        $resetPasswordEmailManager->sendAdminResetPasswordEmail($adminUser, 'en_US')->shouldBeCalledOnce();

        $this(new SendResetPasswordEmail('admin@example.com', 'en_US'));
    }

    public function it_throws_exception_while_handling_if_user_doesnt_exist(
        UserRepositoryInterface $userRepository,
        ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
    ): void {
        $userRepository->findOneByEmail('admin@example.com')->willReturn(null);

        $resetPasswordEmailManager->sendResetPasswordEmail(Argument::cetera())->shouldNotBeCalled();
        $resetPasswordEmailManager->sendAdminResetPasswordEmail(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new SendResetPasswordEmail('admin@example.com', 'en_US')])
        ;
    }
}
