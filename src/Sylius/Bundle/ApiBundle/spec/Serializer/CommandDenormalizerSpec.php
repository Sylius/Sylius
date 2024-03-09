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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandDenormalizerSpec extends ObjectBehavior
{
    function let(DenormalizerInterface $baseNormalizer, AdvancedNameConverterInterface $nameConverter): void
    {
        $this->beConstructedWith($baseNormalizer, $nameConverter);
    }

    function it_implements_context_aware_denormalizer_interface(): void
    {
        $this->shouldImplement(ContextAwareDenormalizerInterface::class);
    }

    function it_supports_denormalization_for_specified_input_class(): void
    {
        $this->supportsDenormalization(null, '', context: ['input' => ['class' => 'Class']])->shouldReturn(true);
    }

    function it_does_not_support_denormalization_for_not_specified_input_class(): void
    {
        $this->supportsDenormalization(null, '')->shouldReturn(false);
    }

    function it_throws_exception_if_not_all_required_parameters_are_present_in_the_context(
        DenormalizerInterface $baseNormalizer,
        AdvancedNameConverterInterface $nameConverter,
    ): void {
        $exception = new MissingConstructorArgumentsException('', 400, null, ['firstName', 'lastName']);
        $context = ['input' => ['class' => RegisterShopUser::class]];
        $data = ['email' => 'test@example.com', 'password' => 'pa$$word'];

        $nameConverter->normalize('firstName', class: RegisterShopUser::class)->willReturn('first_name');
        $nameConverter->normalize('lastName', class: RegisterShopUser::class)->willReturn('lastName');

        $baseNormalizer->denormalize($data, '', null, $context)->willThrow($exception);

        $this
            ->shouldThrow(new MissingConstructorArgumentsException(
                'Request does not have the following required fields specified: first_name, lastName.',
            ))
            ->during('denormalize', [$data, '', null, $context])
        ;
    }

    function it_throws_exception_for_mismatched_argument_type(
        DenormalizerInterface $baseNormalizer,
        AdvancedNameConverterInterface $nameConverter,
    ): void {
        $previousException = NotNormalizableValueException::createForUnexpectedDataType('', 1, ['string'], 'firstName');
        $exception = new UnexpectedValueException('', 400, $previousException);
        $context = ['input' => ['class' => RegisterShopUser::class]];
        $data = ['firstName' => 1];

        $nameConverter->normalize('firstName', class: RegisterShopUser::class)->willReturn('first_name');

        $baseNormalizer->denormalize($data, '', null, $context)->willThrow($exception);

        $this
            ->shouldThrow(new InvalidRequestArgumentException(
                'Request field "first_name" should be of type "string".',
            ))
            ->during('denormalize', [$data, '', null, $context])
        ;
    }

    function it_throws_the_same_exception_if_previous_exception_is_not_normalizable_value_exception(
        DenormalizerInterface $baseNormalizer,
    ): void {
        $exception = new UnexpectedValueException('Unexpected value');
        $context = ['input' => ['class' => RegisterShopUser::class]];
        $data = ['firstName' => '1'];

        $baseNormalizer->denormalize($data, '', null, $context)->willThrow($exception);

        $this
            ->shouldThrow(new UnexpectedValueException('Unexpected value'))
            ->during('denormalize', [$data, '', null, $context])
        ;
    }
}
