<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Provider;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\RequestException;
use Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\Exception\CurrencyNotExistException;

/**
 * Class EuropeanCentralBankProvider
 *
 * Get the currency rate from the European Central Bank
 *
 * @author Milan Popovic <komita1981@gmail.com>
 */
class EuropeanCentralBankProvider implements ProviderInterface
{
    /**
     * Http Client object
     *
     * @var \Guzzle\Http\Client
     */
    private $httpClient;

    /**
     * Service exchange rate url
     *
     * @var string
     */
    private $serviceUrl = 'http://www.ecb.europa.eu/';

    /**
     * Service base currency
     *
     * @var string
     */
    private $baseCurrency = 'EUR';

    /**
     * European Central Bank provider construct
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get rate from European Central Bank exchange rate service
     *
     * @param  string            $currencyFrom
     * @param  string            $currencyTo
     * @throws ProviderException | CurrencyNotExistException
     *
     * @return float
     */
    public function getRate($currencyFrom, $currencyTo)
    {
        $fetchUrl = sprintf('%sstats/eurofxref/eurofxref-daily.xml', $this->serviceUrl);

        try {
            $response = $this->httpClient->get($fetchUrl)->send();
        } catch (RequestException $e) {
            throw new ProviderException($e->getMessage());
        }

        if (!$response) {
            throw new ProviderException('Response from ECB is empty');
        }

        $xmlResponse = $response->xml();

        if (! isset($xmlResponse->Cube->Cube->Cube)) {
            throw new ProviderException('Invalid XML file');
        }

        $currencyFrom == $this->baseCurrency && $currencyFromRate = (float) 1;
        $currencyTo == $this->baseCurrency && $currencyToRate = (float) 1;

        foreach ($xmlResponse->Cube->Cube->Cube as $node) {
            if (! isset($node['currency']) || ! isset($node['rate'])) {
                continue;
            }

            $currency = (string) $node['currency'];

            if ($currencyFrom == $currency) {
                $currencyFromRate = (float) $node['rate'];
            }

            if ($currencyTo == $currency) {
                $currencyToRate = (float) $node['rate'];
            }

            if (isset($currencyFromRate) && isset($currencyToRate)) {
                break;
            }
        }

        if (! isset($currencyFromRate) || ! is_float($currencyFromRate) || $currencyFromRate == 0) {
            throw new CurrencyNotExistException($currencyTo);
        }

        if (! isset($currencyToRate) || ! is_float($currencyToRate) || $currencyToRate == 0) {
            throw new CurrencyNotExistException($currencyTo);
        }

        return (float) $currencyToRate/$currencyFromRate;
    }
}
