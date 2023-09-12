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
    public function findOneByAddress(AddressInterface $address, ?string $scope = null): ?ZoneInterface;

    /** @return ZoneInterface[] */
    public function findAllByAddress(AddressInterface $address, ?string $scope = null): array;

    /**
     * @param array<ZoneInterface> $zones
     *
     * @return array<ZoneInterface>
     */
    public function findAllByZones(array $zones, ?string $scope = null): array;
}
