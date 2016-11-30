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
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResource;
use Sylius\Bundle\ThemeBundle\Translation\Resource\TranslationResourceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslationResourceSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('my-domain.my-locale.my-format');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TranslationResource::class);
    }

    function it_implements_translation_resource_interface()
    {
        $this->shouldImplement(TranslationResourceInterface::class);
    }

    function it_is_a_translation_resource_value_object()
    {
        $this->getName()->shouldReturn('my-domain.my-locale.my-format');
        $this->getDomain()->shouldReturn('my-domain');
        $this->getLocale()->shouldReturn('my-locale');
        $this->getFormat()->shouldReturn('my-format');
    }

    function it_throws_an_invalid_argument_exception_if_failed_to_instantiate_with_given_filepath()
    {
        $this->beConstructedWith('one.dot');

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
