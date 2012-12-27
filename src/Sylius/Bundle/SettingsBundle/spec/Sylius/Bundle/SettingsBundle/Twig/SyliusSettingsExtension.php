<?php

namespace spec\Sylius\Bundle\SettingsBundle\Twig;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius settings extension for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSettingsExtension extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface $settingsManager
     */
    function let($settingsManager)
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

    function it_should_return_settings_by_namespace($settingsManager)
    {
        $settingsManager->loadSettings('general-settings')->shouldBeCalled()->willReturn(array('param' => 'value'));

        $this->getSettings('general-settings')->shouldReturn(array('param' => 'value'));
    }

    function it_should_return_settings_parameter_by_namespace_and_name($settingsManager)
    {
        $settingsManager->loadSettings('general-settings')->shouldBeCalled()->willReturn(array('param' => 'value'));

        $this->getParameter('general-settings', 'param')->shouldReturn('value');
    }
}
