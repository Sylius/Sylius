<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Manager;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SettingsManagerSpec extends ObjectBehavior
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
}
