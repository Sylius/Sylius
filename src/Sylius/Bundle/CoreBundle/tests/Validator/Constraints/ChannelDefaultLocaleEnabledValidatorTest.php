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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelDefaultLocaleEnabled;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelDefaultLocaleEnabledValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ChannelDefaultLocaleEnabledValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private ChannelDefaultLocaleEnabledValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ChannelDefaultLocaleEnabledValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItValidatesOnlyAChannel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $constraint = new ChannelDefaultLocaleEnabled();

        $this->validator->validate(new \stdClass(), $constraint);
    }

    public function testItIsAChannelDefaultLocaleEnabledValidator(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $constraint = $this->createMock(Constraint::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->validator->validate($channel, $constraint);
    }

    public function testItAddsViolationIfDefaultLocaleIsNotEnabledForAGivenChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $locale = $this->createMock(LocaleInterface::class);
        $constraint = new ChannelDefaultLocaleEnabled();

        $channel->method('getDefaultLocale')->willReturn($locale);
        $channel->method('hasLocale')->with($locale)->willReturn(false);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->executionContext->method('buildViolation')->with($constraint->message)->willReturn($violationBuilder);

        $violationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('defaultLocale')
            ->willReturn($violationBuilder);

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate($channel, $constraint);
    }

    public function testItDoesNothingIfDefaultLocaleIsEnabledForAGivenChannel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $locale = $this->createMock(LocaleInterface::class);
        $constraint = new ChannelDefaultLocaleEnabled();

        $channel->method('getDefaultLocale')->willReturn($locale);
        $channel->method('hasLocale')->with($locale)->willReturn(true);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($channel, $constraint);
    }
}
