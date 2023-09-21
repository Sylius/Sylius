<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. o o.o.
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
    public function findOneByAddress(AddressInterface $address, ?string $scope = null): ?ZoneInterface
    {
        $query = $this->createByAddressQueryBuilder($address, $scope);

        $query
            ->addSelect('(CASE
                    WHEN o.type = \'province\' THEN 1
                    WHEN o.type = \'country\' THEN 2
                    WHEN o.type = \'zone\' THEN 3
                    ELSE 4
                END) AS HIDDEN sort_order')
            ->orderBy('sort_order', 'ASC')
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    /** @return ZoneInterface[] */
    public function findByAddress(AddressInterface $address, ?string $scope = null): array
    {
        return $this->createByAddressQueryBuilder($address, $scope)->getQuery()->getResult();
    }

    public function createByAddressQueryBuilder(AddressInterface $address, ?string $scope = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        if (null !== $scope) {
            $query
                ->andWhere($query->expr()->in('o.scope', ':scopes'))
                ->setParameter('scopes', [$scope, Scope::ALL])
            ;
        }

        $orConditions = [];

        if ($address->getCountryCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('o.type', ':country'),
                $query->expr()->eq('members.code', ':countryCode'),
            );

            $query->setParameter('country', ZoneInterface::TYPE_COUNTRY);
            $query->setParameter('countryCode', $address->getCountryCode());
        }

        if ($address->getProvinceCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('o.type', ':province'),
                $query->expr()->eq('members.code', ':provinceCode'),
            );

            $query->setParameter('province', ZoneInterface::TYPE_PROVINCE);
            $query->setParameter('provinceCode', $address->getProvinceCode());
        }

        $query->andWhere($query->expr()->orX(...$orConditions));

        return $query;
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

        $query = $this->createQueryBuilder('o')
            ->select('o', 'members')
            ->leftJoin('o.members', 'members')
        ;

        if (null !== $scope) {
            $query
                ->andWhere($query->expr()->in('o.scope', ':scopes'))
                ->setParameter('scopes', array_unique([$scope, Scope::ALL]))
            ;
        }

        $query
            ->andWhere('o.type = :type')
            ->andWhere($query->expr()->in('members.code', ':zones'))
            ->setParameter('type', ZoneInterface::TYPE_ZONE)
            ->setParameter('zones', $zonesCodes)
        ;

        return $query->getQuery()->getResult();
    }
}
