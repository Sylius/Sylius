<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Security;

use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacVoter implements VoterInterface
{
    /**
     * @var CurrentIdentityProviderInterface
     */
    protected $currentIdentityProvider;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * @var string[]
     */
    protected $permissions;

    /**
     * @var IdentityInterface
     */
    protected $identity;

    /**
     * @param PermissionMapInterface $permissionMap
     * @param RolesResolverInterface $rolesResolver
     */
    public function __construct(PermissionMapInterface $permissionMap, RolesResolverInterface $rolesResolver)
    {
        $this->permissionMap = $permissionMap;
        $this->rolesResolver = $rolesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return 0 !== strpos($attribute, 'ROLE_');
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $result   = VoterInterface::ACCESS_ABSTAIN;
        $identity = $token->getUser();
        if (!$identity instanceof IdentityInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;
            foreach ($this->rolesResolver->getRoles($identity) as $role) {
                if ($this->permissionMap->hasPermission($role, $attribute)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return $result;
    }
}
