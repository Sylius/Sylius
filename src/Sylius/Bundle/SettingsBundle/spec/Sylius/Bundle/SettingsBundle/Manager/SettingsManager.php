<?php

namespace spec\Sylius\Bundle\SettingsBundle\Manager;

use PHPSpec2\ObjectBehavior;

/**
 * Settings manager spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsManager extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Cache\Cache                  $cache
     * @param Doctrine\Common\Persistence\ObjectManager    $manager
     * @param Doctrine\Common\Persistence\ObjectRepository $repository
     */
    function let($cache, $manager, $repository)
    {
        $namespaces = array(
            'general-settings',
            'taxation-settings',
            'seo-settings',
        );

        $this->beConstructedWith($namespaces, $cache, $manager, $repository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface');
    }

    function it_should_fetch_cache_if_available_when_loading_settings($cache)
    {
        $cache->contains('general-settings')->shouldBeCalled()->willReturn(true);
        $cache->fetch('general-settings')->shouldBeCalled()->willReturn(array());

        $this->loadSettings('general-settings')->shouldReturn(array());
    }
}
