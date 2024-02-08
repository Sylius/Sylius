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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class LocaleCodeAwareInputCommandDataTransformerSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext): void
    {
        $this->beConstructedWith($localeContext);
    }

    function it_supports_only_locale_code_aware_interface(
        LocaleCodeAwareInterface $localeCodeAware,
        ResourceInterface $resource,
    ): void {
        $this->supportsTransformation($localeCodeAware)->shouldReturn(true);
        $this->supportsTransformation($resource)->shouldReturn(false);
    }

    function it_adds_locale_code_to_object(
        LocaleContextInterface $localeContext,
        LocaleCodeAwareInterface $command,
    ): void {
        $command->getLocaleCode()->willReturn(null);

        $localeContext->getLocaleCode()->willReturn('en_US');

        $command->setLocaleCode('en_US');

        $this->transform($command, '', [])->shouldReturn($command);
    }

    function it_does_nothing_if_object_has_locale_code(
        LocaleCodeAwareInterface $command,
    ): void {
        $command->getLocaleCode()->willReturn('en_US');

        $command->setLocaleCode('en_US')->shouldNotBeCalled();

        $this->transform($command, '', [])->shouldReturn($command);
    }
}
