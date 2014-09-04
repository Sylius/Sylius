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

class ExchangeRateSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Model\ExchangeRate');
    }

    public function it_implements_Sylius_exchange_rate_interface()
    {
        $this->shouldImplement('Sylius\Bundle\MoneyBundle\Model\ExchangeRateInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_currency_by_default()
    {
        $this->getCurrency()->shouldReturn(null);
    }

    public function its_currency_is_mutable()
    {
        $this->setCurrency('RSD');
        $this->getCurrency()->shouldReturn('RSD');
    }

    public function it_has_no_rate_by_default()
    {
        $this->getRate()->shouldReturn(null);
    }

    public function its_rate_is_mutable()
    {
        $this->setRate(112.75);
        $this->getRate()->shouldReturn(112.75);
    }
}
