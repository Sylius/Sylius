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

use PhpSpec\ObjectBehavior;

class CurrencyContextSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Security\Core\SecurityContextInterface      $securityContext
     * @param Symfony\Component\HttpFoundation\Session\SessionInterface     $session
     * @param Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface $settingsManager
     * @param Doctrine\Common\Persistence\ObjectManager                     $userManager
     * @param Sylius\Bundle\SettingsBundle\Model\Settings                   $settings
     */
    function let($securityContext, $session, $settingsManager, $userManager, $settings)
    {
        $settingsManager->loadSettings('general')->shouldBeCalled()->willReturn($settings);
        $settings->get('currency')->shouldBeCalled()->willReturn('EUR');

        $this->beConstructedWith($securityContext, $session, $settingsManager, $userManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    function it_extends_Sylius_currency_context()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Context\CurrencyContext');
    }

    function it_gets_default_currency()
    {
        $this->getDefaultCurrency()->shouldReturn('EUR');
    }

    /**
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_gets_currency_from_session_if_there_is_no_user($token, $securityContext, $session)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->get('currency', 'EUR')->shouldBeCalled()->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\User                                $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_gets_currency_from_user_if_authenticated($user, $token, $securityContext)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
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
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->set('currency', 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\User                                $user
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     */
    function it_sets_currency_to_user_if_authenticated($user, $token, $securityContext)
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->setCurrency('PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
