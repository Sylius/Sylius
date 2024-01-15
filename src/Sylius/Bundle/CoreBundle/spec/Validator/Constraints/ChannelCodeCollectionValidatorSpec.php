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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollection;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ChannelCodeCollectionValidatorSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($channelRepository);

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
        $context->getRoot()->willReturn(new \stdClass());

        $this
            ->shouldThrow(\LogicException::class)
            ->during('validate', [[], new ChannelCodeCollection(['validateAgainstAllChannels' => false])])
        ;
    }

    function it_retrieves_an_object_from_value_if_value_is_form_and_validates_collections_for_local_channels(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channelMobile,
        ChannelInterface $channelWeb,
        ChannelsAwareInterface $channelsAware,
        ContextualValidatorInterface $contextualValidator,
        ExecutionContextInterface $context,
        Form $form,
        ValidatorInterface $validator,
    ): void {
        $channelWeb->getCode()->willReturn('WEB');
        $channelMobile->getCode()->willReturn('MOBILE');

        $channelsAware->getChannels()->willReturn(new ArrayCollection([
            $channelMobile->getWrappedObject(),
            $channelWeb->getWrappedObject(),
        ]));

        $context->getRoot()->willReturn($form);
        $form->getNormData()->willReturn($channelsAware);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['one', 'two'];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $channelRepository->findAll()->shouldNotBeCalled();

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($value, $collection, $groups)->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
        ]));
    }

    function it_validates_collections_for_local_channels(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ChannelInterface $channelWeb,
        ChannelInterface $channelMobile,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
        ChannelsAwareInterface $channelsAware,
    ): void {
        $channelWeb->getCode()->willReturn('WEB');
        $channelMobile->getCode()->willReturn('MOBILE');

        $channelsAware->getChannels()->willReturn(new ArrayCollection([
            $channelMobile->getWrappedObject(),
            $channelWeb->getWrappedObject(),
        ]));

        $context->getRoot()->willReturn($channelsAware);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['one', 'two'];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $channelRepository->findAll()->shouldNotBeCalled();

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($value, $collection, $groups)->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
        ]));
    }

    function it_does_nothing_when_local_collection_if_channels_is_empty(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ChannelsAwareInterface $channelsAware,
    ): void {
        $channelsAware->getChannels()->willReturn(new ArrayCollection());

        $context->getRoot()->willReturn($channelsAware);

        $channelRepository->findAll()->shouldNotBeCalled();

        $context->getValidator()->shouldNotBeCalled();

        $this->validate([], new ChannelCodeCollection(['validateAgainstAllChannels' => false]));
    }

    function it_validates_collections_for_all_channels(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ChannelInterface $channelWeb,
        ChannelInterface $channelMobile,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $channelWeb->getCode()->willReturn('WEB');
        $channelMobile->getCode()->willReturn('MOBILE');
        $channelRepository->findAll()->willReturn([$channelWeb, $channelMobile]);

        $constraints = [new NotBlank(), new Type('numeric')];
        $groups = ['Default', 'test_group'];
        $value = ['one', 'two'];

        $collection = new Collection(
            [
                'WEB' => $constraints,
                'MOBILE' => $constraints,
            ],
            $groups,
        );

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($value, $collection, $groups)->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($value, new ChannelCodeCollection([
            'constraints' => $constraints,
            'groups' => $groups,
            'validateAgainstAllChannels' => true,
        ]));
    }
}
