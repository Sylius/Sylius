<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Component\Rbac\Context\RbacContextInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;

/**
 * This voter is only triggered when RBAC is disabled. It falls back to symfony
 * standard "security role" based authorization, but leverages Sylius' role hierarchy.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacDisabledVoter implements VoterInterface
{
    /**
     * @var RbacContextInterface
     */
    protected $rbacContext;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * @var array
     */
    protected $adminRoles;

    /**
     * Constructor.
     *
     * @param RbacContextInterface   $rbacContext
     * @param RolesResolverInterface $rolesResolver
     * @param array|string           $adminRoles
     */
    public function __construct(
        RbacContextInterface $rbacContext,
        RolesResolverInterface $rolesResolver,
        $adminRoles = array()
    ) {
        $this->rbacContext = $rbacContext;
        $this->rolesResolver = $rolesResolver;
        $this->adminRoles = is_array($adminRoles) ? $adminRoles : array($adminRoles);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsAttribute($attribute)
    {
        return !$this->rbacContext->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return !$this->rbacContext->isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if ($this->rbacContext->isEnabled()) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $identity = $token->getUser();

        if (!$identity instanceof IdentityInterface) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        foreach ($this->getSecurityRoles($identity) as $role) {
            if (in_array($role, $this->adminRoles)) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }

    /**
     * @param IdentityInterface $identity
     *
     * @return array
     */
    private function getSecurityRoles($identity)
    {
        $securityRoles = array();

        foreach ($this->rolesResolver->getRoles($identity) as $role) {
            $securityRoles = array_merge($securityRoles, array_diff($role->getSecurityRoles(), $securityRoles));
        }

        return $securityRoles;
    }
}
