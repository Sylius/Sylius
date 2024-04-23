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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AdminUserDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $tokenStorage, $passwordUpdater);
    }

    function it_supports_only_admin_user_entity(AdminUserInterface $adminUser, ProductInterface $product): void
    {
        $this->supports($adminUser)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_updates_password_during_persisting_an_admin_user(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        PasswordUpdaterInterface $passwordUpdater,
        AdminUserInterface $adminUser,
    ): void {
        $passwordUpdater->updatePassword($adminUser)->shouldBeCalled();
        $decoratedDataPersister->persist($adminUser, [])->shouldBeCalled();

        $this->persist($adminUser);
    }

    function it_removes_admin_user_if_it_is_different_than_currently_logged_in_admin_user(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
        AdminUserInterface $currentlyLoggedInUser,
        TokenInterface $token,
    ): void {
        $userToBeDeleted->getId()->willReturn(1);

        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(2);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $decoratedDataPersister->remove($userToBeDeleted, [])->shouldBeCalled();

        $this->remove($userToBeDeleted);
    }

    function it_does_not_allow_to_remove_currently_logged_in_admin_user(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
        AdminUserInterface $currentlyLoggedInUser,
        TokenInterface $token,
    ): void {
        $userToBeDeleted->getId()->willReturn(1);

        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(1);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $decoratedDataPersister->remove($userToBeDeleted, [])->shouldNotBeCalled();

        $this
            ->shouldThrow(CannotRemoveCurrentlyLoggedInUser::class)
            ->during('remove', [$userToBeDeleted])
        ;
    }

    function it_removes_admin_user_if_there_is_no_token(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
    ): void {
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn(null);

        $decoratedDataPersister->remove($userToBeDeleted, [])->shouldBeCalled();

        $this->remove($userToBeDeleted);
    }
}
