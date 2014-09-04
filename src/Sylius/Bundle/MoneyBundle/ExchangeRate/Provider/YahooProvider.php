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

/**
 * Class YahooProvider
 *
 * Get the currency rates from the Google Service
 *
 * @author Ivan Đurđevac <djurdjevac@gmail.com>
 */
class YahooProvider implements ProviderInterface
{
    /**
     * Index in service response
     */
    const EXCHANGE_RATE_INDEX = 1;

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
    private $serviceUrl = 'http://finance.yahoo.com/';

    /**
     * Google provider construct
     *
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get rate from Google exchange rate service
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     *
     * @throws ProviderException
     *
     * @return float
     */
    public function getRate($currencyFrom, $currencyTo)
    {
        $fetchUrl = sprintf('%sd/quotes.csv?e=.csv&f=sl1d1t1&s=%s%s=X', $this->serviceUrl, $currencyFrom, $currencyTo);

        try {
            $response = $this->httpClient->get($fetchUrl)->send();
        } catch (RequestException $e) {
            throw new ProviderException($e->getMessage());
        }

        if ($response) {
            $response->getBody()->seek(0);
            $responseArray = explode(',', (string) $response->getBody());

            if (isset($responseArray[self::EXCHANGE_RATE_INDEX]) && (float) $responseArray[self::EXCHANGE_RATE_INDEX] != 0) {
                return (float) $responseArray[self::EXCHANGE_RATE_INDEX];
            }
        }

        throw new ProviderException('Yahoo exchange service is not available.');
    }
}
