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

namespace Sylius\Component\Addressing\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ZoneRepositoryInterface extends RepositoryInterface
{
    public function findOneByAddressAndType(AddressInterface $address, string $type, ?string $scope = null): ?ZoneInterface;

    /** @return ZoneInterface[] */
    public function findByAddress(AddressInterface $address, ?string $scope = null): array;

    public function createByAddressQueryBuilder(AddressInterface $address, ?string $scope = null): QueryBuilder;

    /**
     * @param array<ZoneInterface> $members
     *
     * @return array<ZoneInterface>
     */
    public function findByMembers(array $members, ?string $scope = null): array;
}
