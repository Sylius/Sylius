<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Exception\RoleNotFoundException;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;

/**
 * Default credential provider.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class CredentialProvider implements CredentialProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $roleRepository;

    /**
     * @var RepositoryInterface
     */
    protected $permissionRepository;

    /**
     * @var array
     */
    protected $cache;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $roleRepository
     * @param RepositoryInterface $permissionRepository
     */
    public function __construct(
        RepositoryInterface $roleRepository,
        RepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($code)
    {
        return $this->has('role', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($code)
    {
        if (!$this->hasRole($code)) {
            throw new RoleNotFoundException($code);

        }
        return $this->get('role', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($code)
    {
        return $this->has('permission', $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($code)
    {
        if (!$this->hasPermission($code)) {
            throw new PermissionNotFoundException($code);

        }
        return $this->get('permission', $code);
    }

    /**
     * Get a role or descpription.
     *
     * @param string $type
     * @param string $code
     *
     * @return PermissionInterface|RoleInterface|null
     */
    protected function get($type, $code)
    {
        if (!isset($this->cache[$type])) {
            $this->cache[$type] = array();
        }

        if (!array_key_exists($code, $this->cache[$type])) {
            $this->cache[$type][$code] = $this->{ $type . 'Repository' }->findOneBy(array('code' => $code));
        }

        return $this->cache[$type][$code];
    }

    /**
     * Check whether a role or permission exists.
     *
     * @param string $type
     * @param string $code
     *
     * @return bool
     */
    protected function has($type, $code)
    {
        return null !== $this->get($type, $code);
    }
}
