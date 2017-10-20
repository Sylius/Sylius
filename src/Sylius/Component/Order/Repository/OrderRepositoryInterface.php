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

namespace Sylius\Component\Order\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface OrderRepositoryInterface extends RepositoryInterface
{
    /**
     * @return int
     */
    public function countPlacedOrders(): int;

    /**
     * @param int $count
     *
     * @return array|OrderInterface[]
     */
    public function findLatest(int $count): array;

    /**
     * @param string $number
     *
     * @return OrderInterface|null
     */
    public function findOneByNumber(string $number): ?OrderInterface;

    /**
     * @param string $tokenValue
     *
     * @return OrderInterface|null
     */
    public function findOneByTokenValue(string $tokenValue): ?OrderInterface;

    /**
     * @param mixed $id
     *
     * @return OrderInterface|null
     */
    public function findCartById($id): ?OrderInterface;

    /**
     * @param \DateTimeInterface $terminalDate
     *
     * @return array|OrderInterface[]
     */
    public function findCartsNotModifiedSince(\DateTimeInterface $terminalDate): array;

    /**
     * @return QueryBuilder
     */
    public function createCartQueryBuilder(): QueryBuilder;
}
