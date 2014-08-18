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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Core\Model\User;

class CurrencyContextSpec extends ObjectBehavior
{
    public function let(
        SessionInterface $session,
        SecurityContextInterface $securityContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $userManager,
        Settings $settings
    )
    {
        $settingsManager->loadSettings('general')->shouldBeCalled()->willReturn($settings);
        $settings->get('currency')->shouldBeCalled()->willReturn('EUR');

        $this->beConstructedWith($session, $securityContext, $settingsManager, $userManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    public function it_extends_Sylius_currency_context()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Context\CurrencyContext');
    }

    public function it_gets_default_currency()
    {
        $this->getDefaultCurrency()->shouldReturn('EUR');
    }

    public function it_gets_currency_from_session_if_there_is_no_user(
        TokenInterface $token,
        $securityContext,
        $session
    ) {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->get('currency', 'EUR')->shouldBeCalled()->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    public function it_gets_currency_from_user_if_authenticated(
        User $user,
        TokenInterface $token,
        $securityContext
    ) {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getCurrency()->shouldBeCalled()->willReturn('PLN');

        $this->getCurrency()->shouldReturn('PLN');
    }

    public function it_sets_currency_to_session_if_there_is_no_user(
        TokenInterface $token,
        $securityContext,
        $session
    )
    {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn(null);
        $session->set('currency', 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }

    public function it_sets_currency_to_user_if_authenticated(
        User $user,
        TokenInterface $token,
        $securityContext
    ) {
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->setCurrency('PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
