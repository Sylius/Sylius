<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListenerSpec extends ObjectBehavior
{
    function let(SettingsManagerInterface $settingsManager)
    {
        $this->beConstructedWith($settingsManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\LocaleListener');
    }

    function it_sets_locale_on_request(
        $settingsManager,
        GetResponseEvent $event,
        Settings $settings,
        Request $request
    )
    {
        $settingsManager->loadSettings('general')->willReturn($settings);
        $settings->get('locale')->willReturn('en');

        $event->getRequest()->willReturn($request);
        $request->setLocale('en')->shouldBeCalled();

        $this->setRequestLocale($event);
    }
}
