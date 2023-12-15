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
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Component\Core\Model\Customer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
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

    function it_throws_exception_if_not_all_required_parameters_are_present_in_the_context(
        DenormalizerInterface $baseNormalizer,
    ): void {
        $exception = new MissingConstructorArgumentsException('Missing constructor arguments', 400, null, ['firstName', 'lastName']);
        $context = ['input' => ['class' => RegisterShopUser::class]];
        $data = ['email' => 'test@example.com', 'password' => 'pa$$word'];
        $baseNormalizer->denormalize($data, '', null, $context)->willThrow($exception);

        $this
            ->shouldThrow(new MissingConstructorArgumentsException(
                'Request does not have the following required fields specified: firstName, lastName.',
            ))
            ->during('denormalize', [$data, '', null, $context]);
    }

    function it_throws_exception_for_mismatched_argument_type(
        DenormalizerInterface $baseNormalizer,
        AdvancedNameConverterInterface $nameConverter,
    ): void {
        $nameConverter->normalize('firstName', RegisterShopUser::class)->willReturn('firstName');
        $nameConverter->normalize('lastName', RegisterShopUser::class)->willReturn('lastName');
        $nameConverter->normalize('email', RegisterShopUser::class)->willReturn('email');
        $nameConverter->normalize('password', RegisterShopUser::class)->willReturn('password');
        $nameConverter->normalize('subscribedToNewsletter', RegisterShopUser::class)->willReturn('subscribedToNewsletter');

        $baseNormalizer
            ->denormalize(
                ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@example.com', 'password' => 'pa$$word'],
                Customer::class,
                null,
                ['input' => ['class' => RegisterShopUser::class]],
            )
            ->willReturn(['key' => 'value'])
        ;

        $this->denormalize(
            ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@example.com', 'password' => 'pa$$word'],
            Customer::class,
            null,
            ['input' => ['class' => RegisterShopUser::class]],
        )->shouldReturn(['key' => 'value']);
    }

    function it_denormalizes_data_if_all_required_parameters_are_specified_based_on_their_normalized_names(
        DenormalizerInterface $baseNormalizer,
        NameConverterInterface $nameConverter,
    ): void {
        $baseNormalizer
            ->denormalize(
                ['first_name' => 'John', 'last_name' => 'Doe', 'email_address' => 'test@example.com', 'pass' => 'pa$$word'],
                Customer::class,
                null,
                ['input' => ['class' => RegisterShopUser::class]],
            )
            ->willReturn(['key' => 'value'])
        ;

        $nameConverter->normalize('firstName', RegisterShopUser::class)->willReturn('first_name');
        $nameConverter->normalize('lastName', RegisterShopUser::class)->willReturn('last_name');
        $nameConverter->normalize('email', RegisterShopUser::class)->willReturn('email_address');
        $nameConverter->normalize('password', RegisterShopUser::class)->willReturn('pass');
        $nameConverter->normalize('subscribedToNewsletter', RegisterShopUser::class)->willReturn('hasNewsletter');

        $this->denormalize(
            ['first_name' => 'John', 'last_name' => 'Doe', 'email_address' => 'test@example.com', 'pass' => 'pa$$word'],
            Customer::class,
            null,
            ['input' => ['class' => RegisterShopUser::class]],
        )->shouldReturn(['key' => 'value']);
    }

    function it_does_not_check_parameters_if_there_is_an_object_to_populate(
        DenormalizerInterface $baseNormalizer,
    ): void {
        $baseNormalizer
            ->denormalize(
                [],
                Customer::class,
                null,
                [
                    'input' => ['class' => VerifyShopUser::class],
                    'object_to_populate' => new VerifyShopUser('TOKEN'),
                ],
            )
            ->willReturn(['key' => 'value'])
        ;

        $this
            ->denormalize(
                [],
                Customer::class,
                null,
                [
                    'input' => ['class' => VerifyShopUser::class],
                    'object_to_populate' => new VerifyShopUser('TOKEN'),
                ],
            )
            ->shouldReturn(['key' => 'value'])
        ;
    }

    function it_throws_the_same_exception_if_previous_exception_is_not_not_normalizable_value_exception(
        DenormalizerInterface $baseNormalizer,
    ): void {
        $exception = new UnexpectedValueException('Unexpected value');
        $context = ['input' => ['class' => RegisterShopUser::class]];
        $data = ['firstName' => '1'];
        $baseNormalizer->denormalize($data, '', null, $context)->willThrow($exception);

        $this
            ->shouldThrow(new UnexpectedValueException('Unexpected value'))
            ->during('denormalize', [$data, '', null, $context]);
    }

    function it_implements_context_aware_denormalizer_interface(): void
    {
        $this->shouldImplement(ContextAwareDenormalizerInterface::class);
    }

    function it_supports_denormalization_for_specified_input_class(): void
    {
        $this->supportsDenormalization(null, '', null, ['input' => ['class' => 'Class']])->shouldReturn(true);
    }

    function it_does_not_support_denormalization_for_not_specified_input_class(): void
    {
        $this->supportsDenormalization(null, '', null, [])->shouldReturn(false);
    }
}
