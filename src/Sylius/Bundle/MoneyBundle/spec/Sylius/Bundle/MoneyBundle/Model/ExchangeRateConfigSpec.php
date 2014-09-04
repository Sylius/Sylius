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
use Prophecy\Argument;
use Symfony\Component\Yaml\Parser;

/**
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ExchangeRateConfigSpec extends ObjectBehavior
{
    public function let(Parser $parser)
    {
        $this->beConstructedWith($parser);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Model\ExchangeRateConfig');
    }

    public function if_should_return_array_on_get()
    {
        $this->parser->parse(Argument::any())->willReturn(Argument::type('array'));

        $this->get()->shouldReturn(Argument::type('array'));
    }

    public function it_should_parse_yaml_just_once(Parser $parser)
    {
        $parser->parse(Argument::any())->willReturn(Argument::type('array'));

        $this->get();
        $this->get();

        $parser->parse(Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function it_should_return_service_names_as_array(Parser $parser)
    {
        $parser->parse(Argument::any())->willReturn(array('services' => array('google', 'yahoo')));

        $this->getExchangeServiceNames()->shouldReturn(array('google', 'yahoo'));
    }

}
