<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\ExchangeRate\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Client;
use StdClass;

/**
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class EuropeanCentralBankProviderSpec extends ObjectBehavior
{
    public function let($httpClient)
    {
        $httpClient->beADoubleOf('Guzzle\Http\Client');
        $this->beConstructedWith($httpClient);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\EuropeanCentralBankProvider');
    }

    public function it_should_fetch_rate_from_extern_service(Client $httpClient, Request $request, Response $response)
    {
        $xml = new StdClass();
        $xml->Cube = new StdClass();
        $xml->Cube->Cube = new StdClass();
        $xml->Cube->Cube->Cube = array(
            array('currency'=>'USD', 'rate' => '23.78')
        );

        $httpClient->get(Argument::any())->willReturn($request);
        $request->send()->willReturn($response);
        $response->xml()->willReturn($xml);

        $this->getRate('EUR', 'USD')->shouldReturn((double) '23.78');
    }

    public function it_should_throw_provider_exception_when_response_is_empty(Client $httpClient, Request $request, Response $response)
    {
        $httpClient->get(Argument::any())->willReturn($request);
        $request->send()->willReturn(false);

        $this->shouldThrow('\Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\ProviderException')
            ->duringGetRate('EUR', 'USD');
    }
}
