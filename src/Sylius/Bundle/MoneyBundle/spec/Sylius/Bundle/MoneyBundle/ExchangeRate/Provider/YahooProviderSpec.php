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
use Guzzle\Http\EntityBody;

/**
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class YahooProviderSpec extends ObjectBehavior
{
    public function let($httpClient)
    {
        $httpClient->beADoubleOf('Guzzle\Http\Client');
        $this->beConstructedWith($httpClient);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\YahooProvider');
    }

    public function it_should_fetch_rate_from_extern_service(Client $httpClient, Request $request, Response $response, EntityBody $entityBody)
    {
        $httpClient->get(Argument::any())->willReturn($request);
        $response->getBody()->willReturn($entityBody);
        $entityBody->seek(0)->willReturn(true);
        $request->send()->willReturn($response);
        $entityBody->__toString()->willReturn('rate,23.78');

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
