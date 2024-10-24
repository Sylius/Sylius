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
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelDefaultLocaleEnabled;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ChannelDefaultLocaleEnabledValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }

    function it_validates_only_a_channel(\stdClass $object): void
    {
        $constraint = new ChannelDefaultLocaleEnabled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [$object, $constraint]);
    }

    function it_is_a_channel_default_locale_enabled_validator(ChannelInterface $channel, Constraint $constraint): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('validate', [$channel, $constraint]);
    }

    function it_adds_violation_if_default_locale_is_not_enabled_for_a_given_channel(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $constraint = new ChannelDefaultLocaleEnabled();

        $channel->getDefaultLocale()->willReturn($locale);
        $channel->hasLocale($locale)->willReturn(false);

        $executionContext->buildViolation($constraint->message)->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('defaultLocale')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate($channel, $constraint);
    }

    function it_does_nothing_if_default_locale_is_enabled_for_a_given_channel(
        ExecutionContextInterface $executionContext,
        ChannelInterface $channel,
        LocaleInterface $locale,
    ): void {
        $constraint = new ChannelDefaultLocaleEnabled();

        $channel->getDefaultLocale()->willReturn($locale);
        $channel->hasLocale($locale)->willReturn(true);

        $executionContext->buildViolation($constraint->message)->shouldNotBeCalled();

        $this->validate($channel, $constraint);
    }
}
