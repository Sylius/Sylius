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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AdminUserDataPersisterSpec extends ObjectBehavior
{
    function let(DataPersisterInterface $decoratedDataPersister, TokenStorageInterface $tokenStorage): void
    {
        $this->beConstructedWith($decoratedDataPersister, $tokenStorage);
    }

    function it_supports_only_admin_user_entity(AdminUserInterface $adminUser, ProductInterface $product): void
    {
        $this->supports($adminUser)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_removes_admin_user_if_it_is_different_than_currently_logged_in_admin_user(
        DataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
        AdminUserInterface $currentlyLoggedInUser,
        TokenInterface $token
    ): void {
        $userToBeDeleted->getId()->willReturn(1);

        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(2);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $decoratedDataPersister->remove($userToBeDeleted)->shouldBeCalled();

        $this->remove($userToBeDeleted);
    }

    function it_does_not_allow_to_remove_currently_logged_in_admin_user(
        DataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
        AdminUserInterface $currentlyLoggedInUser,
        TokenInterface $token
    ): void {
        $userToBeDeleted->getId()->willReturn(1);

        $tokenStorage->getToken()->willReturn($token);
        $currentlyLoggedInUser->getId()->willReturn(1);
        $token->getUser()->willReturn($currentlyLoggedInUser);

        $decoratedDataPersister->remove($userToBeDeleted)->shouldNotBeCalled();

        $this
            ->shouldThrow(CannotRemoveCurrentlyLoggedInUser::class)
            ->during('remove', [$userToBeDeleted])
        ;
    }

    function it_removes_admin_user_if_no_user_is_logged_in(
        DataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted,
        TokenInterface $tokenInterface
    ): void {
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUser()->willReturn(null);

        $decoratedDataPersister->remove($userToBeDeleted)->shouldBeCalled();

        $this->remove($userToBeDeleted);
    }

    function it_removes_admin_user_if_there_is_no_token(
        DataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        AdminUserInterface $userToBeDeleted
    ): void {
        $userToBeDeleted->getId()->willReturn(11);

        $tokenStorage->getToken()->willReturn(null);

        $decoratedDataPersister->remove($userToBeDeleted)->shouldBeCalled();

        $this->remove($userToBeDeleted);
    }
}
