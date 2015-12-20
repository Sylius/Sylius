<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Storage\StorageInterface;

class CurrencyContextSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Context\CurrencyContext');
    }

    function it_implements_Sylius_currency_context_interface()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_gets_default_currency()
    {
        $this->getDefaultCurrency()->shouldReturn('EUR');
    }

    function it_gets_currency_from_session($storage)
    {
        $storage->getData(CurrencyContextInterface::STORAGE_KEY, 'EUR')->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    function it_sets_currency_to_session($storage)
    {
        $storage->setData(CurrencyContextInterface::STORAGE_KEY, 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
