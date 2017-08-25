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

namespace Sylius\Bundle\ResourceBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ResourceLogEntryRepositoryInterface
{
    /**
     * @param string $objectId
     *
     * @return QueryBuilder
     */
    public function createByObjectIdQueryBuilder(string $objectId): QueryBuilder;
}
