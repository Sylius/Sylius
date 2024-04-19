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

namespace Sylius\Bundle\AddressingBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

/**
 * @implements ZoneRepositoryInterface<ZoneInterface>
 */
class ZoneRepository extends EntityRepository implements ZoneRepositoryInterface
{
    public function findOneByAddressAndType(AddressInterface $address, string $type, ?string $scope = null): ?ZoneInterface
    {
        $queryBuilder = $this->createByAddressQueryBuilder($address, $scope);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.type', ':type'))
            ->setParameter('type', $type)
            ->setMaxResults(1)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /** @return ZoneInterface[] */
    public function findByAddress(AddressInterface $address, ?string $scope = null): array
    {
        return $this->createByAddressQueryBuilder($address, $scope)->getQuery()->getResult();
    }

    public function createByAddressQueryBuilder(AddressInterface $address, ?string $scope = null): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        if (null !== $scope) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('o.scope', ':scopes'))
                ->setParameter('scopes', array_unique([$scope, Scope::ALL]))
            ;
        }

        $orConditions = [];

        if ($address->getCountryCode() !== null) {
            $orConditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('o.type', ':country'),
                $queryBuilder->expr()->eq('members.code', ':countryCode'),
            );

            $queryBuilder->setParameter('country', ZoneInterface::TYPE_COUNTRY);
            $queryBuilder->setParameter('countryCode', $address->getCountryCode());
        }

        if ($address->getProvinceCode() !== null) {
            $orConditions[] = $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('o.type', ':province'),
                $queryBuilder->expr()->eq('members.code', ':provinceCode'),
            );

            $queryBuilder->setParameter('province', ZoneInterface::TYPE_PROVINCE);
            $queryBuilder->setParameter('provinceCode', $address->getProvinceCode());
        }

        if ($orConditions !== []) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(...$orConditions));
        }

        return $queryBuilder;
    }

    /**
     * @param array<ZoneInterface> $members
     *
     * @return array<ZoneInterface>
     */
    public function findByMembers(array $members, ?string $scope = null): array
    {
        $zonesCodes = array_map(
            fn (ZoneInterface $zone): string => $zone->getCode(),
            $members,
        );

        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        if (null !== $scope) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('o.scope', ':scopes'))
                ->setParameter('scopes', array_unique([$scope, Scope::ALL]))
            ;
        }

        $queryBuilder
            ->andWhere('o.type = :type')
            ->andWhere($queryBuilder->expr()->in('members.code', ':zones'))
            ->setParameter('type', ZoneInterface::TYPE_ZONE)
            ->setParameter('zones', $zonesCodes)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
