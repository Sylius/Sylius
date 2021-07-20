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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CommandNormalizerSpec extends ObjectBehavior
{
    function let(NormalizerInterface $baseNormalizer): void
    {
        $this->beConstructedWith($baseNormalizer);
    }

    function it_implements_context_aware_normalizer_interface(): void
    {
        $this->shouldImplement(ContextAwareNormalizerInterface::class);
    }

    function it_supports_normalization_if_data_has_get_class_method_and_it_is_missing_constructor_arguments_exception(): void
    {
        $this->supportsNormalization(
            new class() { public function getClass(): string { return MissingConstructorArgumentsException::class; }}
        )->shouldReturn(true);
    }

    function it_does_not_support_normalization_if_data_has_no_get_class_method(): void
    {
        $this->supportsNormalization(new \stdClass())->shouldReturn(false);
    }

    function it_does_not_support_normalization_if_data_class_is_not_missing_constructor_arguments_exception(): void
    {
        $this
            ->supportsNormalization(new class() { public function getClass(): string { return \Exception::class; }})
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_normalization_if_normalizer_has_already_been_called(): void
    {
        $this
            ->supportsNormalization(new \stdClass(), null, ['command_normalizer_already_called' => true])
            ->shouldReturn(false)
        ;
    }

    function it_normalizes_response_for_missing_constructor_arguments_exception(
        NormalizerInterface $baseNormalizer,
        \stdClass $object
    ): void {
        $baseNormalizer
            ->normalize($object, null, ['command_normalizer_already_called' => true])
            ->willReturn(['message' => 'Message'])
        ;

        $this->normalize($object)->shouldReturn(['code' => 400, 'message' => 'Message']);
    }
}
