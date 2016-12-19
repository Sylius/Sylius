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

use Doctrine\ORM\QueryBuilder;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface AddressLogEntryRepositoryInterface
{
    /**
     * @param string $objectId
     *
     * @return QueryBuilder
     */
    public function createByObjectIdQueryBuilder($objectId);
}
