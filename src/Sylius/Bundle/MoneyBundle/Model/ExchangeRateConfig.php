<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Model;

use Symfony\Component\Yaml\Parser;

/**
 * Class ExchangeRateConfig
 *
 * Config for exchange rates
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeRateConfig
{
    /**
     * Exchange Rate config
     * @var array
     */
    protected $configArray;

    /**
     * Yaml Parser
     * @var Parser
     */
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get Config Array
     * @return array
     */
    public function get()
    {
        if (!$this->configArray) {
            $this->configArray = $this->parser->parse(file_get_contents(__DIR__ . '/../Resources/config/exchange_rates.yml'));
        }

        return $this->configArray;
    }

    /**
     * Get available exchange rate services
     * @return string
     */
    public function getExchangeServiceNames()
    {
        $config = $this->get();

        return $config['services'];
    }
}
