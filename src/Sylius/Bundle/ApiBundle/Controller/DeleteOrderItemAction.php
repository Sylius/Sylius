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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteOrderItemAction
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function __invoke(Request $request): Response
    {
        $command = new RemoveItemFromCart(
            $request->attributes->get('tokenValue'),
            $request->attributes->get('itemId'),
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse();
    }
}
