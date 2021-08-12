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
use Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyCustomerAccount;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandDenormalizerSpec extends ObjectBehavior
{
    function let(DenormalizerInterface $baseNormalizer, ClassMetadataFactoryInterface $classMetadataFactory): void
    {
        $this->beConstructedWith($baseNormalizer, $classMetadataFactory);
    }

    function it_throws_exception_if_not_all_required_parameters_are_present_in_the_context(
        DenormalizerInterface $baseNormalizer,
        ClassMetadataFactoryInterface $classMetadataFactory,
        ClassMetadataInterface $classMetadata,
        AttributeMetadataInterface $emailAttributeMetadata,
        AttributeMetadataInterface $passwordAttributeMetadata,
        AttributeMetadataInterface $firstNameCodeMetadata,
        AttributeMetadataInterface $lastNameCodeMetadata
    ): void {
        $classMetadataFactory->getMetadataFor(RegisterShopUser::class)->willReturn($classMetadata);

        $classMetadata->getAttributesMetadata()->willReturn([
            $emailAttributeMetadata->getWrappedObject(),
            $passwordAttributeMetadata->getWrappedObject(),
            $firstNameCodeMetadata->getWrappedObject(),
            $lastNameCodeMetadata->getWrappedObject()
        ]);

        $emailAttributeMetadata->getName()->willReturn('email');
        $passwordAttributeMetadata->getName()->willReturn('password');
        $firstNameCodeMetadata->getName()->willReturn('firstName');
        $lastNameCodeMetadata->getName()->willReturn('lastName');

        $emailAttributeMetadata->getSerializedName()->willReturn(null);
        $passwordAttributeMetadata->getSerializedName()->willReturn(null);
        $firstNameCodeMetadata->getSerializedName()->willReturn(null);
        $lastNameCodeMetadata->getSerializedName()->willReturn(null);

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
        DenormalizerInterface $baseNormalizer,
        ClassMetadataFactoryInterface $classMetadataFactory,
        ClassMetadataInterface $classMetadata,
        AttributeMetadataInterface $emailAttributeMetadata,
        AttributeMetadataInterface $passwordAttributeMetadata,
        AttributeMetadataInterface $firstNameCodeMetadata,
        AttributeMetadataInterface $lastNameCodeMetadata
    ): void {
        $classMetadataFactory->getMetadataFor(RegisterShopUser::class)->willReturn($classMetadata);

        $classMetadata->getAttributesMetadata()->willReturn([
            $emailAttributeMetadata->getWrappedObject(),
            $passwordAttributeMetadata->getWrappedObject(),
            $firstNameCodeMetadata->getWrappedObject(),
            $lastNameCodeMetadata->getWrappedObject()
        ]);

        $emailAttributeMetadata->getName()->willReturn('email');
        $passwordAttributeMetadata->getName()->willReturn('password');
        $firstNameCodeMetadata->getName()->willReturn('firstName');
        $lastNameCodeMetadata->getName()->willReturn('lastName');

        $emailAttributeMetadata->getSerializedName()->willReturn(null);
        $passwordAttributeMetadata->getSerializedName()->willReturn(null);
        $firstNameCodeMetadata->getSerializedName()->willReturn(null);
        $lastNameCodeMetadata->getSerializedName()->willReturn(null);

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

    function it_does_not_check_parameters_if_there_is_an_object_to_populate(
        DenormalizerInterface $baseNormalizer
    ): void {
        $baseNormalizer
            ->denormalize(
                [],
                Customer::class,
                null,
                [
                    'input' => ['class' => VerifyCustomerAccount::class],
                    'object_to_populate' => new VerifyCustomerAccount('TOKEN')
                ]
            )
            ->willReturn(['key' => 'value'])
        ;

        $this
            ->denormalize(
                [],
                Customer::class,
                null,
                [
                    'input' => ['class' => VerifyCustomerAccount::class],
                    'object_to_populate' => new VerifyCustomerAccount('TOKEN')
                ]
            )
            ->shouldReturn(['key' => 'value'])
        ;
    }

    function it_implements_context_aware_denormalizer_interface(): void
    {
        $this->shouldImplement(ContextAwareDenormalizerInterface::class);
    }

    function it_supports_denormalization_for_specified_input_class(): void
    {
        $this->supportsDenormalization(
            null,
            Order::class,
            null,
            ['input' => ['class' => AddItemToCart::class]]
        )->shouldReturn(true);
    }

    function it_does_not_support_denormalization_for_not_specified_input_class(): void
    {
        $this->supportsDenormalization(
            null,
            Order::class,
            null,
            []
        )->shouldReturn(false);
    }
}
