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
use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelper;
use Sylius\Bundle\SettingsBundle\Templating\Helper\SettingsHelperInterface;
use Symfony\Component\Templating\Helper\Helper;

final class SettingsHelperSpec extends ObjectBehavior
{
    function let(SettingsManagerInterface $settingsManager)
    {
        $this->beConstructedWith($settingsManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SettingsHelper::class);
    }

    function it_implements_settings_helper_interface()
    {
        $this->shouldImplement(SettingsHelperInterface::class);
    }

    function it_is_a_twig_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_returns_settings_by_namespace(SettingsManagerInterface $settingsManager, SettingsInterface $settings)
    {
        $settingsManager->load('sylius_taxation')->willReturn($settings);

        $this->getSettings('sylius_taxation')->shouldReturn($settings);
    }
}
