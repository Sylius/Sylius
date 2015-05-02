<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Security;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserLoginSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Security\UserLogin');
    }

    function it_implements_user_login_interface()
    {
        $this->shouldImplement('Sylius\Bundle\UserBundle\Security\UserLoginInterface');
    }

     function it_logs_user_in($container, UserInterface $user, SecurityContextInterface $context, SessionInterface $session)
     {
         //TODO cleanup
         $user->getRoles()->willReturn(array('ROLE_TEST'));
//         $user->serialize(Argument::any())->shouldBeCalled();

         $container->get('security.context')->willReturn($context);
//         $container->get('session')->willReturn($session);

         $context->setToken(Argument::type('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'))->shouldBeCalled();
//         $session->set('_security_main', Argument::any())->shouldBeCalled();

         $this->login($user);
     }
}
