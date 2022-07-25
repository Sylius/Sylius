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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $this->beConstructedWith($userRepository, $metadata, $passwordUpdater);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_resets_password(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($shopUser);
        $metadata->getParameter('resetting')->willReturn(['token' => ['ttl' => 'P5D']]);

        $shopUser->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '5';
        }))->willReturn(true);

        $shopUser->getPasswordResetToken()->willReturn('TOKEN');

        $shopUser->setPlainPassword('newPassword')->shouldBeCalled();
        $passwordUpdater->updatePassword($shopUser)->shouldBeCalled();
        $shopUser->setPasswordResetToken(null)->shouldBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this->__invoke($command);
    }

    function it_throws_exception_if_token_is_expired(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($shopUser);
        $metadata->getParameter('resetting')->willReturn(['token' => ['ttl' => 'P5D']]);

        $shopUser->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '5';
        }))->willReturn(false);

        $shopUser->getPasswordResetToken()->willReturn('TOKEN');
        $shopUser->setPlainPassword('newPassword')->shouldNotBeCalled();
        $passwordUpdater->updatePassword($shopUser)->shouldNotBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }

    function it_throws_exception_if_tokens_are_not_exact(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
        MetadataInterface $metadata,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $userRepository->findOneBy(['passwordResetToken' => 'TOKEN'])->willReturn($shopUser);
        $metadata->getParameter('resetting')->willReturn(['token' => ['ttl' => 'P5D']]);

        $shopUser->isPasswordRequestNonExpired(Argument::that(function (\DateInterval $dateInterval) {
            return $dateInterval->format('%d') === '5';
        }))->willReturn(false);

        $shopUser->getPasswordResetToken()->willReturn('BADTOKEN');
        $shopUser->setPlainPassword('newPassword')->shouldNotBeCalled();
        $passwordUpdater->updatePassword($shopUser)->shouldNotBeCalled();

        $command = new ResetPassword('TOKEN');
        $command->newPassword = 'newPassword';
        $command->resetPasswordToken = 'TOKEN';

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$command])
        ;
    }
}
