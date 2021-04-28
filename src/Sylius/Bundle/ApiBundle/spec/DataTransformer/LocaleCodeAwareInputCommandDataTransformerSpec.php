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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use ApiPlatform\Core\Api\IriConverterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class LocaleCodeAwareInputCommandDataTransformerSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($localeContext, $iriConverter);
    }

    function it_supports_only_locale_code_aware_interface(
        LocaleCodeAwareInterface $localeCodeAware,
        ResourceInterface $resource
    ): void {
        $this->supportsTransformation($localeCodeAware)->shouldReturn(true);
        $this->supportsTransformation($resource)->shouldReturn(false);
    }

    function it_adds_locale_code_to_object(
        LocaleContextInterface $localeContext,
        LocaleCodeAwareInterface $command
    ): void {
        $command->getLocale()->willReturn(null);

        $localeContext->getLocaleCode()->willReturn('en_US');

        $command->setLocaleCode('en_US');

        $this->transform($command, '', [])->shouldReturn($command);
    }

    function it_changes_locale_iri_to_locale_code(
        LocaleCodeAwareInterface $command,
        IriConverterInterface $iriConverter,
        LocaleInterface $locale
    ): void {
        $command->getLocale()->willReturn('api/v2/shop/en_US');
        $iriConverter->getItemFromIri('api/v2/shop/en_US')->willReturn($locale);
        $locale->getCode()->willReturn('en_US');

        $command->setLocaleCode('en_US')->shouldBeCalled();

        $this->transform($command, '', [])->shouldReturn($command);
    }
}
