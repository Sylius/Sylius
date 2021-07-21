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
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\RegisterShopUser;
use Sylius\Component\Core\Model\Customer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandDenormalizerSpec extends ObjectBehavior
{
    function let(DenormalizerInterface $baseNormalizer): void
    {
        $this->beConstructedWith($baseNormalizer);
    }

    function it_throws_exception_if_not_all_required_parameters_are_present_in_the_context(
        DenormalizerInterface $baseNormalizer
    ): void {
        $baseNormalizer->denormalize(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new MissingConstructorArgumentsException(
                'Request does not have the following required fields specified: firstName, lastName.'
            ))
            ->during(
                'denormalize',
                [
                    ['email' => 'test@example.com', 'password' => 'pa$$word'],
                    '',
                    null,
                    ['input' => ['class' => RegisterShopUser::class]]
                ]
            )
        ;
    }

    function it_denormalizes_data_if_all_required_parameters_are_specified(
        DenormalizerInterface $baseNormalizer
    ): void {
        $baseNormalizer
            ->denormalize(
                ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@example.com', 'password' => 'pa$$word'],
                Customer::class,
                null,
                ['input' => ['class' => RegisterShopUser::class]]
            )
            ->willReturn(['key' => 'value'])
        ;

        $this->denormalize(
            ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@example.com', 'password' => 'pa$$word'],
            Customer::class,
            null,
            ['input' => ['class' => RegisterShopUser::class]]
        )->shouldReturn(['key' => 'value']);
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
