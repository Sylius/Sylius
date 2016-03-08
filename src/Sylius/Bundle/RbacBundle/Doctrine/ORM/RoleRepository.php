<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Repository\RoleRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChildRoles(RoleInterface $role)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where($queryBuilder->expr()->lt('o.left', $role->getRight()))
            ->andWhere($queryBuilder->expr()->gt('o.left', $role->getLeft()))
            ->getQuery()
            ->execute()
        ;
    }
}
