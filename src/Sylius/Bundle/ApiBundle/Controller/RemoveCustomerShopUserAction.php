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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Command\Customer\RemoveShopUser;
use Sylius\Bundle\ApiBundle\Exception\UserNotFoundException;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class RemoveCustomerShopUserAction
{
    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     */
    public function __construct(
        private MessageBusInterface $commandBus,
        private UserRepositoryInterface $shopUserRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $customerId = $request->attributes->get('id', '');

        $user = $this->shopUserRepository->findOneBy(['customer' => $customerId]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $this->commandBus->dispatch(new RemoveShopUser($user->getId()));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
