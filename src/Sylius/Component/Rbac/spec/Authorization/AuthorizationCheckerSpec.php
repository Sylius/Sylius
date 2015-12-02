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
use Prophecy\Argument;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AuthorizationCheckerSpec extends ObjectBehavior
{
    function let(
        CurrentIdentityProviderInterface $currentIdentityProvider,
        VoterInterface $voter
    ) {
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
        $currentIdentityProvider->getIdentity()->willReturn(null);

        $this->isGranted('edit_product')->shouldReturn(false);
    }
}
