<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Rbac\Authorization;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Sylius\Component\Rbac\Authorization\Voter\RbacVoterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AuthorizationCheckerSpec extends ObjectBehavior
{
    function let(CurrentIdentityProviderInterface $currentIdentityProvider, RbacVoterInterface $voter)
    {
        $this->beConstructedWith($currentIdentityProvider, $voter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Authorization\AuthorizationChecker');
    }

    function it_implements_Sylius_Rbac_authorization_checker_interface()
    {
        $this->shouldImplement('Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface');
    }

    function it_obtains_the_current_identity_and_returns_false_if_none_available($currentIdentityProvider)
    {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn(null);

        $this->isGranted('edit_product')->shouldReturn(false);
    }

    function it_returns_false_if_none_of_current_identity_roles_has_permission(
        $currentIdentityProvider,
        IdentityInterface $identity,
        $voter
    ) {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn($identity);

        $voter->isGranted($identity, 'can_close_store', null)->shouldBeCalled()->willReturn(false);
        $voter->isGranted($identity, 'can_close_store', null)->shouldBeCalled()->willReturn(false);

        $this->isGranted('can_close_store')->shouldReturn(false);
    }

    function it_returns_true_if_any_of_current_identity_roles_has_permission(
        $currentIdentityProvider,
        IdentityInterface $identity,
        $voter
    ) {
        $currentIdentityProvider->getIdentity()->shouldBeCalled()->willReturn($identity);

        $voter->isGranted($identity, 'can_open_store', null)->shouldBeCalled()->willReturn(false);
        $voter->isGranted($identity, 'can_open_store', null)->shouldBeCalled()->willReturn(true);

        $this->isGranted('can_open_store')->shouldReturn(true);
    }
}