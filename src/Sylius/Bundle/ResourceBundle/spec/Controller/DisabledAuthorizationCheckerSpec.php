<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DisabledAuthorizationCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\DisabledAuthorizationChecker');
    }

    function it_implements_resource_controller_authorization_checker_interface()
    {
        $this->shouldImplement(AuthorizationCheckerInterface::class);
    }
    
    function it_always_returns_true(RequestConfiguration $requestConfiguration)
    {
        $this->isGranted($requestConfiguration, 'create')->shouldReturn(true);
        $this->isGranted($requestConfiguration, 'update')->shouldReturn(true);
        $this->isGranted($requestConfiguration, 'custom')->shouldReturn(true);
    }
}
