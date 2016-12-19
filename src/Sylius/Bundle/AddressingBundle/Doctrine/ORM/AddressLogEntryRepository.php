<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AddressLogEntryRepository extends EntityRepository implements AddressLogEntryRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createByObjectIdQueryBuilder($objectId)
    {
        return $this->createQueryBuilder('log')
            ->where('log.objectId = :objectId')
            ->setParameter('objectId', $objectId)
        ;
    }
}
