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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;

final readonly class OrderItemAdjustmentsProvider implements ProviderInterface
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Collection|array
    {
        if (false === isset($uriVariables['id'], $uriVariables['tokenValue'])) {
            return [];
        }

        $orderItem = $this->orderItemRepository->findOneByIdAndOrderTokenValue(
            (int) $uriVariables['id'],
            (string) $uriVariables['tokenValue'],
        );

        if (null === $orderItem) {
            return [];
        }

        return $orderItem->getAdjustmentsRecursively($context['request']->query->get('type'));
    }
}
