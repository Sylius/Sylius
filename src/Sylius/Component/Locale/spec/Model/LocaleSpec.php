<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Locale\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

final class LocaleSpec extends ObjectBehavior
{
    function let(): void
    {
        \Locale::setDefault('en');
    }

    function it_implements_a_locale_interface(): void
    {
        $this->shouldImplement(LocaleInterface::class);
    }

    function it_is_timestampable(): void
    {
        $this->shouldImplement(TimestampableInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('de_DE');
        $this->getCode()->shouldReturn('de_DE');
    }

    function it_has_a_name(): void
    {
        $this->setCode('pl_PL');
        $this->getName()->shouldReturn('Polish (Poland)');
        $this->getName('es')->shouldReturn('polaco (Polonia)');

        $this->setCode('pl');
        $this->getName()->shouldReturn('Polish');
        $this->getName('es')->shouldReturn('polaco');
    }

    function it_returns_name_when_converted_to_string(): void
    {
        $this->setCode('pl_PL');
        $this->__toString()->shouldReturn('Polish (Poland)');

        $this->setCode('pl');
        $this->__toString()->shouldReturn('Polish');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_does_not_have_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
