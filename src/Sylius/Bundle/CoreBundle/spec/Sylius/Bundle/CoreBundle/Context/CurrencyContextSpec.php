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
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CurrencyContextSpec extends ObjectBehavior
{
    function let(
        StorageInterface $storage,
        SecurityContextInterface $securityContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $userManager,
        Settings $settings
    ) {
        $settingsManager->loadSettings('general')->willReturn($settings);
        $settings->get('currency')->willReturn('EUR');

        $this->beConstructedWith($storage, $securityContext, $settingsManager, $userManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Context\CurrencyContext');
    }

    function it_extends_Sylius_currency_context()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Context\CurrencyContext');
    }

    function it_gets_default_currency()
    {
        $this->getDefaultCurrency()->shouldReturn('EUR');
    }

    function it_gets_currency_from_session_if_there_is_no_user(
        TokenInterface $token,
        $securityContext,
        $storage
    ) {
        $securityContext->getToken()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn(null);

        $storage->getData(CurrencyContextInterface::STORAGE_KEY, 'EUR')->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    function it_gets_currency_from_user_if_authenticated(
        UserInterface $user,
        TokenInterface $token,
        $securityContext
    ) {
        $securityContext->getToken()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn($user);

        $user->getCurrency()->willReturn('PLN');

        $this->getCurrency()->shouldReturn('PLN');
    }

    function it_sets_currency_to_session_if_there_is_no_user(
        TokenInterface $token,
        $securityContext,
        $storage
    ) {
        $securityContext->getToken()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn(null);

        $storage->setData(CurrencyContextInterface::STORAGE_KEY, 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }

    function it_sets_currency_to_user_if_authenticated(
        UserInterface $user,
        TokenInterface $token,
        $securityContext
    ) {
        $securityContext->getToken()->willReturn($token);
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $token->getUser()->willReturn($user);

        $user->setCurrency('PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
