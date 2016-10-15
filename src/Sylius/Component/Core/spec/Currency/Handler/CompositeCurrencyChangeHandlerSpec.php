<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Currency\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Currency\Handler\CompositeCurrencyChangeHandler;
use Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;

/**
 * @mixin CompositeCurrencyChangeHandler
 * 
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CompositeCurrencyChangeHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeCurrencyChangeHandler::class);
    }

    function it_implements_a_currency_change_handler_interface()
    {
        $this->shouldImplement(CurrencyChangeHandlerInterface::class);
    }

    function it_throws_a_handle_exception_when_there_are_no_nested_handlers_defined()
    {
        $this->shouldThrow(HandleException::class)->during('handle', ['USD']);
    }

    function it_throws_a_handle_exception_when_any_of_nested_handlers_throws_it(
        CurrencyChangeHandlerInterface $currencyChangeHandler
    ) {
        $currencyChangeHandler->handle(Argument::any())->willThrow(HandleException::class);
        $this->addHandler($currencyChangeHandler);

        $this->shouldThrow(HandleException::class)->during('handle', ['USD']);
    }

    function it_uses_every_nested_handler_to_handle_the_currency_change(
        CurrencyChangeHandlerInterface $firstCurrencyChangeHandler,
        CurrencyChangeHandlerInterface $secondCurrencyChangeHandler
    ) {
        $firstCurrencyChangeHandler->handle('USD')->shouldBeCalled();
        $secondCurrencyChangeHandler->handle('USD')->shouldBeCalled();

        $this->addHandler($firstCurrencyChangeHandler);
        $this->addHandler($secondCurrencyChangeHandler);

        $this->handle('USD');
    }
}
