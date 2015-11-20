<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Doctrine\Common\Cache\Cache;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoterSpec extends ObjectBehavior
{
    function let(
        PermissionMapInterface $permissionMap,
        PermissionProviderInterface $permissionProvider,
        RolesResolverInterface $rolesResolver,
        Cache $cache
    ) {
        $this->beConstructedWith($permissionMap, $permissionProvider, $rolesResolver, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Security\RbacVoter');
    }

    function it_is_a_symfony_security_voter()
    {
        $this->shouldHaveType('Symfony\Component\Security\Core\Authorization\Voter\VoterInterface');
    }

    function it_denies_access_if_none_of_current_identity_roles_has_permission(
        TokenInterface $token,
        IdentityInterface $identity,
        $permissionMap,
        $rolesResolver,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $token->getUser()->shouldBeCalled()->willReturn($identity);
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn(array($role1, $role2));

        $permissionMap->hasPermission($role1, 'can_close_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_close_store')->shouldBeCalled()->willReturn(false);

        $this->vote($token, null, array('can_close_store'))->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_grants_access_if_any_of_current_identity_roles_has_permission(
        TokenInterface $token,
        IdentityInterface $identity,
        $permissionMap,
        $rolesResolver,
        RoleInterface $role1,
        RoleInterface $role2
    ) {
        $token->getUser()->shouldBeCalled()->willReturn($identity);
        $rolesResolver->getRoles($identity)->shouldBeCalled()->willReturn(array($role1, $role2));

        $permissionMap->hasPermission($role1, 'can_close_store')->shouldBeCalled()->willReturn(false);
        $permissionMap->hasPermission($role2, 'can_close_store')->shouldBeCalled()->willReturn(true);

        $this->vote($token, null, array('can_close_store'))->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }
}