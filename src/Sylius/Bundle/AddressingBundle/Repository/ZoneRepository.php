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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

/**
 * @implements ZoneRepositoryInterface<ZoneInterface>
 */
class ZoneRepository extends EntityRepository implements ZoneRepositoryInterface
{
    /** @return ZoneInterface[] */
    public function findAllByAddress(AddressInterface $address, ?string $scope = null): array
    {
        $query = $this->createQueryBuilder('z')
            ->select('z')
            ->leftJoin('z.members', 'm')
        ;

        if (null !== $scope) {
            $query
                ->andWhere('z.scope = :scope')
                ->setParameter('scope', $scope)
            ;
        }

        $orConditions = [];

        if ($address->getCountryCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('z.type', ':country'),
                $query->expr()->eq('m.code', ':countryCode'),
            );

            $query->setParameter('country', 'country');
            $query->setParameter('countryCode', $address->getCountryCode());
        }

        if ($address->getProvinceCode() !== null) {
            $orConditions[] = $query->expr()->andX(
                $query->expr()->eq('z.type', ':province'),
                $query->expr()->eq('m.code', ':provinceCode'),
            );

            $query->setParameter('province', 'province');
            $query->setParameter('provinceCode', $address->getProvinceCode());
        }

        $query->andWhere($query->expr()->orX(...$orConditions));

        return $query->getQuery()->getResult();
    }
}
