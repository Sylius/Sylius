<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;
use Doctrine\Common\Cache\Cache;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoter implements RbacVoterInterface
{
    const DEFAULT_TTL = 60;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var PermissionProviderInterface
     */
    protected $permissionProvider;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * @var Cache
     */
    protected $permissionsCache;

    /**
     * @var int
     */
    protected $ttl;

    public function __construct(
        PermissionMapInterface $permissionMap,
        PermissionProviderInterface $permissionProvider,
        RolesResolverInterface $rolesResolver,
        Cache $permissionsCache,
        $ttl = self::DEFAULT_TTL
    ) {
        $this->permissionMap      = $permissionMap;
        $this->permissionProvider = $permissionProvider;
        $this->rolesResolver      = $rolesResolver;
        $this->permissionsCache   = $permissionsCache;
        $this->ttl                = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        $key = $this->getCacheKey($attribute);

        if ($this->permissionsCache->contains($key)) {
            return $this->permissionsCache->fetch($key);
        }

        try {
            $this->permissionProvider->getPermission($attribute);
            $this->permissionsCache->save($key, true, $this->ttl);
            return true;
        } catch (PermissionNotFoundException $e) {
            $this->permissionsCache->save($key, false, $this->ttl);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return in_array('Sylius\Component\Rbac\Model\IdentityInterface', class_implements($class));
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $identity = $token->getUser();

        if (!$identity instanceof IdentityInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $deny = false;

        foreach ($attributes as $permissionCode) {
            if (!$this->supportsAttribute($permissionCode)) {
                continue;
            }

            $deny = true;

            if ($this->hasPermission($identity, $permissionCode, $object)) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $deny ? VoterInterface::ACCESS_DENIED : VoterInterface::ACCESS_ABSTAIN;
    }

    protected function hasPermission(IdentityInterface $identity, $permissionCode, $object)
    {
        $roles = $this->rolesResolver->getRoles($identity);

        foreach ($roles as $role) {
            if ($this->permissionMap->hasPermission($role, $permissionCode)) {
                return true;
            }
        }

        return false;
    }

    private function getCacheKey($attribute)
    {
        return 'voter.'.$attribute;
    }
}
