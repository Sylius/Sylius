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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Dashboard;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

final class NewOrdersComponent
{
    public const DEFAULT_LIMIT = 5;

    public int $limit = self::DEFAULT_LIMIT;

    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @return array<OrderInterface>
     */
    #[ExposeInTemplate]
    public function getNewOrders(): array
    {
        return $this->orderRepository->findLatest($this->limit);
    }
}
