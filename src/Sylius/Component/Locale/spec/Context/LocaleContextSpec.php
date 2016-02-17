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
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * @mixin \Sylius\Component\Locale\Context\LocaleContext
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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

    function it_is_Sylius_locale_context()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_gets_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('pl_PL');
    }

    function it_can_set_locale_to_storage(StorageInterface $storage)
    {
        $storage->setData(Argument::any(), 'en_GB')->shouldBeCalled();

        $this->setCurrentLocale('en_GB');
    }

    function it_can_get_current_locale(StorageInterface $storage)
    {
        $storage->setData(Argument::any(), 'en_US')->shouldBeCalled()->willReturn();

        $this->setCurrentLocale('en_US');

        $storage->getData(Argument::cetera())->shouldBeCalled()->willReturn('en_US');

        $this->getCurrentLocale()->shouldReturn('en_US');
    }

    function its_current_locale_is_default_locale_by_default(StorageInterface $storage)
    {
        $storage->getData(Argument::cetera())->shouldBeCalled()->willReturn('pl_PL');

        $this->getCurrentLocale()->shouldReturn('pl_PL');
    }
}
