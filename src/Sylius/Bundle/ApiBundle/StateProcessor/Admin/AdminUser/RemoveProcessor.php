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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\AdminUser;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveCurrentlyLoggedInUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

final readonly class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, AdminUserInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        if ($this->isTryingToRemoveLoggedInUser($data)) {
            throw new CannotRemoveCurrentlyLoggedInUser();
        }

        $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }

    private function isTryingToRemoveLoggedInUser(UserInterface $user): bool
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return false;
        }

        /** @var UserInterface $loggedUser */
        $loggedUser = $token->getUser();

        return $loggedUser->getId() === $user->getId();
    }
}
