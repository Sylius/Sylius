<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Currency\Context\CurrencyContextInterface;
use Sylius\Storage\StorageInterface;

class CurrencyContextSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Currency\Context\CurrencyContext');
    }

    function it_implements_Sylius_currency_context_interface()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_gets_default_currency_code()
    {
        $this->getDefaultCurrencyCode()->shouldReturn('EUR');
    }

    function it_gets_currency_code_from_session($storage)
    {
        $storage->getData(CurrencyContextInterface::STORAGE_KEY, 'EUR')->willReturn('RSD');

        $this->getCurrencyCode()->shouldReturn('RSD');
    }

    function it_sets_currency_code_to_session($storage)
    {
        $storage->setData(CurrencyContextInterface::STORAGE_KEY, 'PLN')->shouldBeCalled();

        $this->setCurrencyCode('PLN');
    }
}
