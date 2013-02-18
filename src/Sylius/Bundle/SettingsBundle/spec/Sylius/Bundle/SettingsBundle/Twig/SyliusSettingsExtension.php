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

    /**
     * @param Sylius\Bundle\SettingsBundle\Model\SettingsInterface $settings
     */
    function it_should_return_settings_by_namespace($settingsManager, $settings)
    {
        $settingsManager->loadSettings('taxation')->shouldBeCalled()->willReturn($settings);

        $this->getSettings('taxation')->shouldReturn($settings);
    }

    /**
     * @param Sylius\Bundle\SettingsBundle\Model\SettingsInterface $settings
     */
    function it_should_return_settings_parameter_by_namespace_and_name($settingsManager, $settings)
    {
        $settingsManager->loadSettings('shipping')->shouldBeCalled()->willReturn($settings);
        $settings->get('price')->willReturn(19.99);

        $this->getSettingsParameter('shipping', 'price')->shouldReturn(19.99);
    }
}
