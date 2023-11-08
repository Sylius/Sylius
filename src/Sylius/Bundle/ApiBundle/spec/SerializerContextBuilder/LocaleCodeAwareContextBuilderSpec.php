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
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LocaleCodeAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $localeContext);
    }

    function it_sets_locale_code_as_a_constructor_argument(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $localeContext->getLocaleCode()->willReturn('en_US');

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn([
                'input' => ['class' => SendContactRequest::class],
                AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                    SendContactRequest::class => ['localeCode' => 'en_US'],
                ],
            ])
        ;
    }

    function it_does_nothing_if_there_is_no_input_class(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn([])
        ;

        $localeContext->getLocaleCode()->shouldNotBeCalled();

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_nothing_if_input_class_is_no_channel_aware(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        LocaleContextInterface $localeContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $localeContext->getLocaleCode()->shouldNotBeCalled();

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn(['input' => ['class' => \stdClass::class]])
        ;
    }
}
