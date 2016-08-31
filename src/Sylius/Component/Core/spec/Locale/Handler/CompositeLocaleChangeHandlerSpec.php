<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Locale\Handler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\CompositeLocaleChangeHandler;
use Sylius\Component\Core\Locale\Handler\LocaleChangeHandlerInterface;

/**
 * @mixin CompositeLocaleChangeHandler
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CompositeLocaleChangeHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeLocaleChangeHandler::class);
    }

    function it_implements_locale_change_handler_interface()
    {
        $this->shouldImplement(LocaleChangeHandlerInterface::class);
    }

    function it_throws_handle_exception_if_there_are_no_nested_handlers_defined()
    {
        $this->shouldThrow(HandleException::class)->during('handle', ['en_GB']);
    }

    function it_throws_handle_exception_if_some_of_the_nested_handler_throws_it(
        LocaleChangeHandlerInterface $localeChangeHandler
    ) {
        $localeChangeHandler->handle('en_GB')->willThrow(HandleException::class);
        $this->addHandler($localeChangeHandler);

        $this->shouldThrow(HandleException::class)->during('handle', ['en_GB']);
    }

    function it_handles_locale_changing_by_all_nested_handlers(
        LocaleChangeHandlerInterface $firstLocaleChangeHandler,
        LocaleChangeHandlerInterface $secondLocaleChangeHandler
    ) {
        $firstLocaleChangeHandler->handle('en_GB')->shouldBeCalled();
        $secondLocaleChangeHandler->handle('en_GB')->shouldBeCalled();

        $this->addHandler($firstLocaleChangeHandler);
        $this->addHandler($secondLocaleChangeHandler);

        $this->handle('en_GB');
    }
}
