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
use Prophecy\Argument;
use Sylius\Component\Storage\StorageInterface;

class LocaleContextSpec extends ObjectBehavior
{
    function let(StorageInterface $storage)
    {
        $this->beConstructedWith($storage, 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Locale\Context\LocaleContext');
    }

    function it_implements_Sylius_locale_context_interface()
    {
        $this->shouldImplement('Sylius\Component\Locale\Context\LocaleContextInterface');
    }

    function it_gets_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('pl_PL');
    }

    function it_gets_locale_from_session($storage)
    {
        $storage->getData(Argument::any(), 'pl_PL')->willReturn('en_US');

        $this->getLocale()->shouldReturn('en_US');
    }

    function it_sets_locale_to_session($storage)
    {
        $storage->setData(Argument::any(), 'en_GB')->shouldBeCalled();

        $this->setLocale('en_GB');
    }
}
