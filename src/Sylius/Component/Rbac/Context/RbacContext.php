<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Context;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Exception\RoleNotFoundException;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacContext implements RbacContextInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $permissionRepository;

    /**
     * @var bool
     */
    protected $rbacInitialized;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $roleRepository
     * @param bool                $rbacInitialized
     */
    public function __construct(
        RepositoryInterface $permissionRepository,
        $rbacInitialized = null
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->rbacInitialized = $rbacInitialized;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (null === $this->rbacInitialized) {
            $this->rbacInitialized = count($this->permissionRepository->findAll()) > 0;
        }

        return $this->rbacInitialized;
    }
}
