<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepository;

/**
 * UserGroup rule configuration form type.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class GroupRepository extends ResourceRepository
{
    /**
     * Get the query builder used in select form.
     *
     * @return QueryBuilder
     */
    public function getFormQueryBuilder()
    {
        return $this->objectRepository->createQueryBuilder('o');
    }
}
