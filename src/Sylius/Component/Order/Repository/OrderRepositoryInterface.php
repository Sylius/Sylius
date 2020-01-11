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
    public function countPlacedOrders(): int;

    /**
     * @return array|OrderInterface[]
     */
    public function findLatest(int $count): array;

    public function findOneByNumber(string $number): ?OrderInterface;

    public function findOneByTokenValue(string $tokenValue): ?OrderInterface;

    public function findCartById($id): ?OrderInterface;

    /**
     * @return array|OrderInterface[]
     */
    public function findCartsNotModifiedSince(\DateTimeInterface $terminalDate): array;

    public function createCartQueryBuilder(): QueryBuilder;
}
