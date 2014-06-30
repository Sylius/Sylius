<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Model;

use PhpSpec\ObjectBehavior;

class CurrencySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Model\Currency');
    }

    function it_implements_Sylius_currency_interface()
    {
        $this->shouldImplement('Sylius\Component\Currency\Model\CurrencyInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('RSD');
        $this->getCode()->shouldReturn('RSD');
    }

    function it_has_no_exchange_rate_by_default()
    {
        $this->getExchangeRate()->shouldReturn(null);
    }

    function its_exchange_rate_is_mutable()
    {
        $this->setExchangeRate(1.1275);
        $this->getExchangeRate()->shouldReturn(1.1275);
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
