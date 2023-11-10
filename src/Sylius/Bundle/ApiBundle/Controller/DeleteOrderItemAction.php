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

use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Bundle\ApiBundle\Exception\OrderItemNotFoundException;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteOrderItemAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private OrderItemRepositoryInterface $orderItemRepository,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $orderItemId = $request->attributes->get('itemId');
        $tokenValue = $request->attributes->get('tokenValue');
        if (null === $orderItemId || null === $tokenValue) {
            throw new OrderItemNotFoundException();
        }

        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue($orderItemId, $tokenValue);
        if ($orderItem === null) {
            throw new OrderItemNotFoundException();
        }

        $this->commandBus->dispatch(new RemoveItemFromCart($tokenValue, $orderItemId));

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
