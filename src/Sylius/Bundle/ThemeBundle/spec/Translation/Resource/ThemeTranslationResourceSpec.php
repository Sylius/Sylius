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

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

final class ThemeTranslationResourceSpec extends ObjectBehavior
{
    function let(ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith($theme, 'my-domain.my-locale.my-format');
    }

    function it_implements_translation_resource_interface(): void
    {
        $this->shouldImplement(TranslationResourceInterface::class);
    }

    function it_is_a_translation_resource_value_object(): void
    {
        $this->getName()->shouldReturn('my-domain.my-locale.my-format');
        $this->getDomain()->shouldReturn('my-domain');
        $this->getLocale()->shouldReturn('my-locale@theme-name');
        $this->getFormat()->shouldReturn('my-format');
    }

    function it_throws_an_invalid_argument_exception_if_failed_to_instantiate_with_given_filepath(ThemeInterface $theme): void
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith($theme, 'one.dot');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
