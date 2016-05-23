<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\RbacBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RbacHelperSpec extends ObjectBehavior
{
    function let(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->beConstructedWith($authorizationChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\RbacBundle\Templating\Helper\RbacHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_uses_authorization_checker_to_verify_permissions($authorizationChecker)
    {
        $authorizationChecker->isGranted('can_block_users')->shouldBeCalled()->willReturn(true);
        $this->isGranted('can_block_users')->shouldReturn(true);

        $authorizationChecker->isGranted('can_eat_bananas')->shouldBeCalled()->willReturn(false);
        $this->isGranted('can_eat_bananas')->shouldReturn(false);
    }
}
