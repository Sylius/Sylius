<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Locale\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Context\CompositeLocaleContext;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeLocaleContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeLocaleContext::class);
    }

    function it_implements_locale_context_interface()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_throws_a_locale_not_found_exception_if_there_are_no_nested_locale_contexts_defined()
    {
        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_a_locale_not_found_exception_if_none_of_nested_locale_contexts_returned_a_locale(
        LocaleContextInterface $localeContext
    ) {
        $localeContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);

        $this->addContext($localeContext);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_returns_first_result_returned_by_nested_request_resolvers(
        LocaleContextInterface $firstLocaleContext,
        LocaleContextInterface $secondLocaleContext,
        LocaleContextInterface $thirdLocaleContext
    ) {
        $firstLocaleContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);
        $secondLocaleContext->getLocaleCode()->willReturn('en_US');
        $thirdLocaleContext->getLocaleCode()->shouldNotBeCalled();

        $this->addContext($firstLocaleContext);
        $this->addContext($secondLocaleContext);
        $this->addContext($thirdLocaleContext);

        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function its_nested_request_resolvers_can_have_priority(
        LocaleContextInterface $firstLocaleContext,
        LocaleContextInterface $secondLocaleContext,
        LocaleContextInterface $thirdLocaleContext
    ) {
        $firstLocaleContext->getLocaleCode()->shouldNotBeCalled();
        $secondLocaleContext->getLocaleCode()->willReturn('pl_PL');
        $thirdLocaleContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);

        $this->addContext($firstLocaleContext, -5);
        $this->addContext($secondLocaleContext, 0);
        $this->addContext($thirdLocaleContext, 5);

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
