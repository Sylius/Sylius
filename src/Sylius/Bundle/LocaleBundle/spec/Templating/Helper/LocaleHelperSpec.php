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

namespace spec\Sylius\Bundle\LocaleBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

final class LocaleHelperSpec extends ObjectBehavior
{
    public function let(LocaleConverterInterface $localeConverter): void
    {
        $this->beConstructedWith($localeConverter);
    }

    public function it_is_a_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    public function it_is_a_locale_helper(): void
    {
        $this->shouldImplement(LocaleHelperInterface::class);
    }

    public function it_converts_locales_code_to_name(LocaleConverterInterface $localeConverter): void
    {
        $localeConverter->convertCodeToName('fr_FR')->willReturn('French (France)');

        $this->convertCodeToName('fr_FR')->shouldReturn('French (France)');
    }

    public function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
