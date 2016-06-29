<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Rbac\Provider;

use Sylius\Rbac\Exception\PermissionNotFoundException;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * Default permission provider uses repository to find the permission.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionProvider implements PermissionProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($code)
    {
        if (null === $permission = $this->repository->findOneBy(['code' => $code])) {
            throw new PermissionNotFoundException($code);
        }

        return $permission;
    }
}
