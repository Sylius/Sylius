<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Provider\ThemeTranslationResource;
use Sylius\Bundle\ThemeBundle\Translation\Provider\TranslationResourceInterface;

/**
 * @mixin ThemeTranslationResource
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeTranslationResourceSpec extends ObjectBehavior
{
    function let(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith($theme, 'my-domain.my-locale.my-format');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\Provider\ThemeTranslationResource');
    }

    function it_implements_translation_resource_interface()
    {
        $this->shouldImplement(TranslationResourceInterface::class);
    }

    function it_is_a_translation_resource_value_object()
    {
        $this->getName()->shouldReturn('my-domain.my-locale.my-format');
        $this->getDomain()->shouldReturn('my-domain');
        $this->getLocale()->shouldReturn('my-locale_theme-name');
        $this->getFormat()->shouldReturn('my-format');
    }

    function it_throws_an_invalid_argument_exception_if_failed_to_instantiate_with_given_filepath(ThemeInterface $theme)
    {
        $theme->getName()->willReturn('theme/name');

        $this->beConstructedWith($theme, 'one.dot');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
