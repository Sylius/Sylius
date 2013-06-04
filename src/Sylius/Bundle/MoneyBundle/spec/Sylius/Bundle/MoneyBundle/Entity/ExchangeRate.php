<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Entity;

use PHPSpec2\ObjectBehavior;

class ExchangeRate extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Entity\ExchangeRate');
    }

    function it_implements_Sylius_exchange_rate_interface()
    {
        $this->shouldImplement('Sylius\Bundle\MoneyBundle\Model\ExchangeRateInterface');
    }

    function it_extends_Sylius_exchange_rate_model()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Model\ExchangeRate');
    }
}
