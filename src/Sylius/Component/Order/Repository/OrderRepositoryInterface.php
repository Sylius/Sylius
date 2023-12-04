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

namespace Sylius\Component\Order\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of OrderInterface
 *
 * @extends RepositoryInterface<T>
 */
interface OrderRepositoryInterface extends RepositoryInterface
{
    public function countPlacedOrders(): int;

    /**
     * @return array|OrderInterface[]
     */
    public function findLatest(int $count): array;

    public function findLatestCart(): ?OrderInterface;

    public function findOneByNumber(string $number): ?OrderInterface;

    public function findOneByTokenValue(string $tokenValue): ?OrderInterface;

    /** @deprecated since Sylius 1.9 and  will be removed in Sylius 2.0, use src/Sylius/Component/Core/Repository/OrderRepositoryInterface instead */
    public function findCartByTokenValue(string $tokenValue): ?OrderInterface;

    public function findCartById($id): ?OrderInterface;

    /**
     * @return array|OrderInterface[]
     */
    public function findCartsNotModifiedSince(\DateTimeInterface $terminalDate, ?int $limit = null): array;

    public function createCartQueryBuilder(): QueryBuilder;

    public function findAllExceptCarts(): array;
}
