<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Converter;

use PHPSpec2\ObjectBehavior;

class CurrencyConverter extends ObjectBehavior
{
    /**
     * @return Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $repository
     */
    function let($repository)
    {
        $this->beConstructedWith('EUR', $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Converter\CurrencyConverter');
    }

    function it_implements_Sylius_exchange_rate_interface()
    {
        $this->shouldImplement('Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface');
    }
}
