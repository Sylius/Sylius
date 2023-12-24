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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\HttpFoundation\Request;

final class GetOrderAdjustmentsAction
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function __invoke(Request $request, string $tokenValue): Collection
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
        $type = $request->query->get('type');

        return $order->getAdjustmentsRecursively($type);
    }
}
