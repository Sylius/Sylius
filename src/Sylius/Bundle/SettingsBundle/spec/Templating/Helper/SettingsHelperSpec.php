<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelperInterface;
use Symfony\Component\Templating\Helper\Helper;

class SettingsHelperSpec extends ObjectBehavior
{
    function let(SettingsManagerInterface $settingsManager)
    {
        $this->beConstructedWith($settingsManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelper');
    }

    function it_implements_settings_helper_interface()
    {
        $this->shouldImplement(SettingsHelperInterface::class);
    }

    function it_should_be_a_Twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_should_return_settings_by_namespace(SettingsManagerInterface $settingsManager, SettingsInterface $settings)
    {
        $settingsManager->load('sylius_taxation')->willReturn($settings);

        $this->getSettings('sylius_taxation')->shouldReturn($settings);
    }
}
