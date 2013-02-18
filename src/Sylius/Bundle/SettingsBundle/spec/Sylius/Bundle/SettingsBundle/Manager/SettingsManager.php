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
     * @param Sylius\Bundle\SettingsBundle\Schema\SchemaRegistryInterface $registry
     * @param Doctrine\Common\Cache\Cache                                 $cache
     * @param Doctrine\Common\Persistence\ObjectManager                   $manager
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface      $repository
     */
    function let($registry, $cache, $manager, $repository)
    {
        $this->beConstructedWith($registry, $manager, $repository, $cache);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Manager\SettingsManager');
    }

    function it_should_be_a_Sylius_settings_manager()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface');
    }

    /**
     * @param Sylius\Bundle\SettingsBundle\Model\SettingsInterface $settings
     */
    function it_should_fetch_cache_if_available_when_loading_settings($cache, $settings)
    {
        $cache->contains('general')->shouldBeCalled()->willReturn(true);
        $cache->fetch('general')->shouldBeCalled()->willReturn($settings);

        $this->loadSettings('general')->shouldReturn($settings);
    }
}
