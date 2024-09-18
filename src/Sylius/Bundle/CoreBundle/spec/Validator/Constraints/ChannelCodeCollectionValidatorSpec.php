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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ChannelCodeCollectionValidatorSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        PropertyAccessorInterface $propertyAccessor,
        ExecutionContextInterface $context,
    ): void {
        $this->beConstructedWith($channelRepository, $propertyAccessor);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_channel_code_collection(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [[], $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_array(): void
    {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', ['', new ChannelCodeCollection()])
        ;
    }

    function it_throws_exception_when_validating_using_local_channels_and_object_does_not_implement_channels_aware_interface(
        ExecutionContextInterface $context,
    ): void {
        $context->getObject()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\LogicException::class)
            ->during('validate', [[], new ChannelCodeCollection([
                'validateAgainstAllChannels' => false,
                'channelAwarePropertyPath' => 'promotion',
            ])])
        ;
    }

    function it_validates_the_value_channels_existence(
        ChannelRepositoryInterface $channelRepository,
        PropertyAccessorInterface $propertyAccessor,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder,
        ContextualValidatorInterface $contextualValidator,
        ChannelsAwareInterface $channelsAware,
        Form $form,
        ValidatorInterface $validator,
    ): void {
        $context->getObject()->willReturn($form);
        $propertyAccessor->getValue($form, 'shippingMethod')->willReturn($channelsAware);
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $channelRepository->findAllWithBasicData()->willReturn([
            ['code' => 'WEB'],
            ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['does_not_exist' => ['one']];

        $constraint = new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'shippingMethod',
        ]);

        $context
            ->buildViolation($constraint->invalidChannelMessage)
            ->shouldBeCalled()
            ->willReturn($violationBuilder)
        ;
        $violationBuilder
            ->setParameter('{{ channel_code }}', 'does_not_exist')
            ->shouldBeCalled()
            ->willReturn($violationBuilder)
        ;
        $violationBuilder->addViolation()->shouldBeCalled();

        $this->validate($value, $constraint);
    }

    function it_retrieves_an_object_from_value_and_validates_collections_for_local_channels(
        ChannelRepositoryInterface $channelRepository,
        PropertyAccessorInterface $propertyAccessor,
        ContextualValidatorInterface $contextualValidator,
        ExecutionContextInterface $context,
        ChannelsAwareInterface $channelsAware,
        Form $form,
        ValidatorInterface $validator,
    ): void {
        $context->getObject()->willReturn($form);
        $propertyAccessor->getValue($form, 'shippingMethod')->willReturn($channelsAware);
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $channelRepository->findAllWithBasicData()->willReturn([
            ['code' => 'WEB'],
            ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];
        $constraint = new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'shippingMethod',
        ]);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();
        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator
            ->validate($value, $collection, $groups)
            ->shouldBeCalled()
            ->willReturn($contextualValidator)
        ;

        $this->validate($value, $constraint);
    }

    function it_validates_collections_for_channels_from_value(
        ChannelRepositoryInterface $channelRepository,
        PropertyAccessorInterface $propertyAccessor,
        ExecutionContextInterface $context,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
        ChannelsAwareInterface $channelsAware,
    ): void {
        $context->getObject()->willReturn($channelsAware);
        $propertyAccessor->getValue($channelsAware, 'promotion')->willReturn($channelsAware);
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $channelRepository->findAllWithBasicData()->willReturn([
            ['code' => 'WEB'],
            ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $context->buildViolation(Argument::any())->shouldNotBeCalled();
        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator
            ->validate($value, $collection, $groups)
            ->shouldBeCalled()
            ->willReturn($contextualValidator)
        ;

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    function it_validates_collections_for_local_channels_and_from_value(
        ChannelRepositoryInterface $channelRepository,
        PropertyAccessorInterface $propertyAccessor,
        ExecutionContextInterface $context,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
        ChannelsAwareInterface $channelsAware,
        ChannelInterface $channel,
    ): void {
        $context->getObject()->willReturn($channelsAware);
        $propertyAccessor->getValue($channelsAware, 'promotion')->willReturn($channelsAware);
        $channel->getCode()->willReturn('WEB');
        $channelsAware->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $channelRepository->findAllWithBasicData()->willReturn([
            ['code' => 'WEB'],
            ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['MOBILE' => ['two']];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $context->buildViolation(Argument::any())->shouldNotBeCalled();
        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator
            ->validate($value, $collection, $groups)
            ->shouldBeCalled()
            ->willReturn($contextualValidator)
        ;

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    function it_does_nothing_when_local_collection_if_channels_is_empty(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ChannelsAwareInterface $channelsAware,
        PropertyAccessorInterface $propertyAccessor,
    ): void {
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $context->getObject()->willReturn($channelsAware);
        $propertyAccessor->getValue($channelsAware, 'promotion')->willReturn($channelsAware);

        $channelRepository->findAllWithBasicData()->willReturn([]);

        $context->buildViolation(Argument::any())->shouldNotBeCalled();
        $context->getValidator()->shouldNotBeCalled();

        $this->validate([], new ChannelCodeCollection([
            'validateAgainstAllChannels' => false,
            'channelAwarePropertyPath' => 'promotion',
        ]));
    }

    function it_validates_collections_for_all_channels(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
        ChannelsAwareInterface $channelsAware,
    ): void {
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $channelRepository->findAllWithBasicData()->willReturn([
            ['code' => 'WEB'],
            ['code' => 'MOBILE'],
        ]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['WEB' => ['one'], 'MOBILE' => ['two']];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator
            ->validate($value, $collection, $groups)
            ->willReturn($contextualValidator)
        ;

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'validateAgainstAllChannels' => true,
        ]));
    }
}
