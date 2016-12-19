<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ResourceLogEntryRepository extends EntityRepository implements ResourceLogEntryRepositoryInterface
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
