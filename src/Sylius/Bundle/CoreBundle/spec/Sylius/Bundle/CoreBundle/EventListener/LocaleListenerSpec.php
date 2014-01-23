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

class LocaleListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface $settingsManager
     */
    function let($settingsManager)
    {
        $this->beConstructedWith($settingsManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\LocaleListener');
    }

    /**
     * @param Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param Sylius\Bundle\SettingsBundle\Model\Settings         $settings
     * @param Symfony\Component\HttpFoundation\Request            $request
     */
    function it_sets_locale_on_request($settingsManager, $event, $settings, $request)
    {
        $settingsManager->loadSettings('general')->willReturn($settings);
        $settings->get('locale')->willReturn('en');

        $event->getRequest()->willReturn($request);
        $request->setLocale('en')->shouldBeCalled();

        $this->setRequestLocale($event);
    }
}
