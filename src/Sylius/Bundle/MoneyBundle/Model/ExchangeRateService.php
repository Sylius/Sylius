<?php namespace Sylius\Bundle\MoneyBundle\Model;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

/**
 * Class ExchangeRateService
 *
 * Exchange Rate Service Model
 * Services are stored into yml file and model get data from Config object
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeRateService
{

    /**
     * Settings Manager
     * @var SettingsManagerInterface
     */
    protected $settingsManager;

    /**
     * Exchange Rate Config
     * @var ExchangeRateConfig
     */
    protected $config;

    /**
     * @param $settingsManager
     * @param $config
     */
    public function __construct(SettingsManagerInterface $settingsManager, ExchangeRateConfig $config)
    {
        $this->settingsManager = $settingsManager;
        $this->config = $config;
    }

    /**
     * Get Active Provider Key
     * Key is container service key at the sam time
     */
    public function getActiveProviderKey()
    {
        $providerName  = $this->settingsManager->loadSettings('exchange_rates')->get('exchange_service_name');
        if (!$providerName) {
            $config = $this->config->get();

            return $config['default_service'];
        }

        return $providerName;
    }

    /**
     * Get active Provider Name
     * @return string
     */
    public function getActiveProviderName()
    {
        $providerKey = $this->getActiveProviderKey();
        $services = $this->config->getExchangeServiceNames();

        return isset($services[$providerKey]) ? $services[$providerKey] : '';
    }
}
