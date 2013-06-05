<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PHPSpec2\ObjectBehavior;

class CurrencyContext extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Security\Core\SecurityContextInterface  $securityContext
     * @param Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param use Doctrine\Common\Persistence\ObjectManager             $userManager
     */
    function let($securityContext, $session, $userManager)
    {
        $this->beConstructedWith($securityContext, $session, $userManager, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    function it_extends_Sylius_currency_context()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Context\CurrencyContext');
    }

    /**
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_gets_currency_from_session_if_there_is_no_user($token, $securityContext, $session)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->get('currency', 'EUR')->shouldBeCalled()->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Entity\User                                $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_gets_currency_from_user_if_authenticated($user, $token, $securityContext)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCurrency()->shouldBeCalled()->willReturn('PLN');

        $this->getCurrency()->shouldReturn('PLN');
    }

    /**
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_sets_currency_to_session_if_there_is_no_user($token, $securityContext, $session)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->set('currency', 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Entity\User                                $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_sets_currency_to_user_if_authenticated($user, $token, $securityContext)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->setCurrency('PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
