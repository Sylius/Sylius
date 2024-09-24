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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class ChangeShopUserPasswordHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(PasswordUpdaterInterface $passwordUpdater, UserRepositoryInterface $userRepository): void
    {
        $this->beConstructedWith($passwordUpdater, $userRepository);
    }

    function it_updates_user_password(
        PasswordUpdaterInterface $passwordUpdater,
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
    ): void {
        $userRepository->find(42)->willReturn($shopUser);

        $shopUser->setPlainPassword('PLAIN_PASSWORD')->shouldBeCalled();
        $passwordUpdater->updatePassword($shopUser)->shouldBeCalled();

        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );

        $this($changePasswordShopUser);
    }

    function it_throws_exception_if_new_passwords_do_not_match(
        PasswordUpdaterInterface $passwordUpdater,
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
    ): void {
        $userRepository->find(Argument::any())->shouldNotBeCalled();

        $shopUser->setPlainPassword(Argument::any())->shouldNotBeCalled();
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'WRONG_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePasswordShopUser])
        ;
    }

    function it_throws_exception_if_shop_user_has_not_been_found(
        PasswordUpdaterInterface $passwordUpdater,
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
    ): void {
        $userRepository->find(42)->willReturn(null);

        $shopUser->setPlainPassword(Argument::any())->shouldNotBeCalled();
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $changePasswordShopUser = new ChangeShopUserPassword(
            newPassword: 'PLAIN_PASSWORD',
            confirmNewPassword: 'PLAIN_PASSWORD',
            currentPassword: 'OLD_PASSWORD',
            shopUserId: 42,
        );

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePasswordShopUser])
        ;
    }
}
