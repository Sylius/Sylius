<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;

final class LocaleContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        LocaleContextInterface $localeContext,
    ): void {
        $this->beConstructedWith($decoratedSerializerContextBuilder, $localeContext);
    }

    function it_updates_an_context_when_locale_context_has_locale(
        Request $request,
        SerializerContextBuilderInterface $decoratedSerializerContextBuilder,
        LocaleContextInterface $localeContext,
    ): void {
        $decoratedSerializerContextBuilder->createFromRequest($request, true, [])->shouldBeCalled();
        $localeContext->getLocaleCode()->willReturn('en_US');

        $this->createFromRequest($request, true, []);
    }
}
