<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSettingsExtensionSpec extends ObjectBehavior
{
    function let(SettingsManagerInterface $settingsManager)
    {
        $this->beConstructedWith($settingsManager);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Twig\SyliusSettingsExtension');
    }

    function it_should_be_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_should_return_settings_by_namespace($settingsManager, Settings $settings)
    {
        $settingsManager->loadSettings('taxation')->shouldBeCalled()->willReturn($settings);

        $this->getSettings('taxation')->shouldReturn($settings);
    }

    function it_should_return_settings_parameter_by_namespace_and_name($settingsManager, Settings $settings)
    {
        $settingsManager->loadSettings('shipping')->shouldBeCalled()->willReturn($settings);
        $settings->get('price')->shouldBeCalled()->willReturn(19.99);

        $this->getSettingsParameter('shipping.price')->shouldReturn(19.99);
    }
}
