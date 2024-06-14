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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Delete;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AdminUserProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $removeProcessor,
        TokenStorageInterface $tokenStorage,
    ): void {
        $this->beConstructedWith($removeProcessor, $tokenStorage);
    }

    function it_processes_delete_operation(
        AdminUserInterface $adminUser,
        ProcessorInterface $removeProcessor,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $loggedUser,
    ): void {
        $operation = new Delete();
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($loggedUser);
        $loggedUser->getId()->willReturn(2);
        $adminUser->getId()->willReturn(1);

        $removeProcessor->process($adminUser, $operation, [], [])->shouldBeCalled();

        $this->process($adminUser, $operation, [], []);
    }

    function it_throws_exception_when_trying_to_delete_logged_in_user(
        AdminUserInterface $adminUser,
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $loggedUser,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($loggedUser);
        $loggedUser->getId()->willReturn(1);
        $adminUser->getId()->willReturn(1);

        $this->shouldThrow(CannotRemoveCurrentlyLoggedInUser::class)->during('process', [$adminUser, new Delete(), [], []]);
    }

    function it_processes_delete_if_no_user_token_found(
        AdminUserInterface $adminUser,
        ProcessorInterface $removeProcessor,
        TokenStorageInterface $tokenStorage,
    ): void {
        $operation = new Delete();
        $tokenStorage->getToken()->willReturn(null);

        $removeProcessor->process($adminUser, $operation, [], [])->shouldBeCalled();

        $this->process($adminUser, $operation, [], []);
    }
}
