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
    public function findOneByAddress(AddressInterface $address, ?string $scope = null): ?ZoneInterface
    {
        $query = $this->getFindByAddressQueryBuilder($address, $scope);

        $query
            ->addSelect('(CASE
                    WHEN z.type = \'province\' THEN 1
                    WHEN z.type = \'country\' THEN 2
                    WHEN z.type = \'zone\' THEN 3
                    ELSE 4
                END) AS HIDDEN sort_order')
            ->orderBy('sort_order', 'ASC')
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    /** @return ZoneInterface[] */
    public function findAllByAddress(AddressInterface $address, ?string $scope = null): array
    {
        return $this->getFindByAddressQueryBuilder($address, $scope)->getQuery()->getResult();
    }

    private function getFindByAddressQueryBuilder(AddressInterface $address, ?string $scope = null): QueryBuilder
    {
        $query = $this->createQueryBuilder('z')
            ->select('z', 'm')
            ->leftJoin('z.members', 'm')
        ;

        if (null !== $scope) {
            $query
                ->andWhere($query->expr()->in('z.scope', ':scopes'))
                ->setParameter('scopes', [$scope, Scope::ALL])
            ;
        }

        $orConditions = [];

        if ($address->getCountryCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('z.type', ':country'),
                $query->expr()->eq('m.code', ':countryCode'),
            );

            $query->setParameter('country', ZoneInterface::TYPE_COUNTRY);
            $query->setParameter('countryCode', $address->getCountryCode());
        }

        if ($address->getProvinceCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('z.type', ':province'),
                $query->expr()->eq('m.code', ':provinceCode'),
            );

            $query->setParameter('province', ZoneInterface::TYPE_PROVINCE);
            $query->setParameter('provinceCode', $address->getProvinceCode());
        }

        $query->andWhere($query->expr()->orX(...$orConditions));

        return $query;
    }

    /**
     * @param array<ZoneInterface> $zones
     *
     * @return array<ZoneInterface>
     */
    public function findAllByZones(array $zones, ?string $scope = null): array
    {
        $zones = array_map(
            fn (ZoneInterface $zone): string => $zone->getCode(),
            $zones,
        );

        $query = $this->createQueryBuilder('z')
            ->select('z', 'm')
            ->leftJoin('z.members', 'm')
        ;

        if (null !== $scope) {
            $query
                ->andWhere('z.scope = :scope')
                ->setParameter('scope', $scope)
            ;
        }

        $query
            ->andWhere('z.type = :type')
            ->andWhere($query->expr()->in('m.code', ':zones'))
            ->setParameter('type', ZoneInterface::TYPE_ZONE)
            ->setParameter('zones', $zones)
        ;

        return $query->getQuery()->getResult();
    }
}
