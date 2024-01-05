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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ExistingChannelCode;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ExistingChannelCodeValidatorSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($channelRepository);

        $this->initialize($context);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_throws_an_exception_if_value_is_not_a_string_or_null(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
    ): void {
        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();

        $context->buildViolation((new ExistingChannelCode())->message)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [new \stdClass(), new ExistingChannelCode()])
        ;
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_existing_channel_code(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        Constraint $constraint,
    ): void {
        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();

        $context->buildViolation((new ExistingChannelCode())->message)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['channel_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_null(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
    ): void {
        $channelRepository->findOneByCode(Argument::any())->shouldNotBeCalled();

        $context->buildViolation((new ExistingChannelCode())->message)->shouldNotBeCalled();

        $this->validate(null, new ExistingChannelCode());
    }

    function it_adds_a_violation_if_channel_with_given_code_does_not_exist(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $channelRepository->findOneByCode('channel_code')->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation((new ExistingChannelCode())->message)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('channel_code', new ExistingChannelCode());
    }

    function it_does_not_add_violation_if_channel_with_given_code_exists(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $context,
    ): void {
        $channelRepository->findOneByCode('channel_code')->willReturn(new Channel());

        $context->buildViolation((new ExistingChannelCode())->message)->shouldNotBeCalled();

        $this->validate('channel_code', new ExistingChannelCode());
    }
}
