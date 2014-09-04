<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\MoneyBundle\Model\ExchangeRateConfig;
use Sylius\Bundle\SettingsBundle\Model\Settings;

/**
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeRateServiceSpec extends ObjectBehavior
{
    public function let(SettingsManagerInterface $settingsManager, ExchangeRateConfig $config)
    {
        $this->beConstructedWith($settingsManager, $config);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Model\ExchangeRateService');
    }

    public function it_should_return_default_key_if_cant_find_settings(
        SettingsManagerInterface $settingsManager, Settings $settings, ExchangeRateConfig $config
    )
    {
        $settingsManager->loadSettings('exchange_rates')->willReturn($settings);
        $settings->get('exchange_service_name')->willReturn(false);

        $config->get()->shouldBeCalled();

        $this->getActiveProviderKey();
    }

    public function it_should_return_key_from_settings_if_they_are_saved(
        SettingsManagerInterface $settingsManager, Settings $settings, ExchangeRateConfig $config
    )
    {
        $settingsManager->loadSettings('exchange_rates')->willReturn($settings);
        $settings->get('exchange_service_name')->willReturn('key_is_stored_in_database_settings');

        $config->get()->shouldNotBeCalled();

        $this->getActiveProviderKey()->shouldReturn('key_is_stored_in_database_settings');
    }

    public function it_should_return_provider_name_if_key_exists(
        SettingsManagerInterface $settingsManager, Settings $settings, ExchangeRateConfig $config
    )
    {
        $settingsManager->loadSettings('exchange_rates')->willReturn($settings);
        $settings->get('exchange_service_name')->willReturn('provider_key');
        $config->getExchangeServiceNames()->willReturn(array('provider_key' => 'Provider Famous Name'));

        $this->getActiveProviderName()->shouldReturn('Provider Famous Name');
    }

    public function it_should_return_empty_string_if_provider_key_do_not_exists(
        SettingsManagerInterface $settingsManager, Settings $settings, ExchangeRateConfig $config
    )
    {
        $settingsManager->loadSettings('exchange_rates')->willReturn($settings);
        $settings->get('exchange_service_name')->willReturn('provider_key');
        $config->getExchangeServiceNames()->willReturn(array('another_provider_key' => 'Provider Famous Name'));

        $this->getActiveProviderName()->shouldReturn('');
    }
}
