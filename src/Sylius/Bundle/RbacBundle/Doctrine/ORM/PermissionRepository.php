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
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Repository\PermissionRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionRepository extends EntityRepository implements PermissionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChildPermissions(PermissionInterface $permission)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where($queryBuilder->expr()->lt('o.left', $permission->getRight()))
            ->andWhere($queryBuilder->expr()->gt('o.left', $permission->getLeft()))
            ->getQuery()
            ->execute()
        ;
    }
}
