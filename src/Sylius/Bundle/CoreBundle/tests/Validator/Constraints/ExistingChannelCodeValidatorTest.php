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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ExistingChannelCode;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ExistingChannelCodeValidator;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ExistingChannelCodeValidatorTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private ExistingChannelCodeValidator $validator;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new ExistingChannelCodeValidator($this->channelRepository);
        $this->validator->initialize($this->executionContext);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ExistingChannelCodeValidator::class, $this->validator);
    }

    public function testItThrowsAnExceptionIfValueIsNotAStringOrNull(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(new \stdClass(), new ExistingChannelCode());
    }

    public function testItThrowsAnExceptionIfConstraintIsNotAnInstanceOfExistingChannelCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('channel_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsNull(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, new ExistingChannelCode());
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new ExistingChannelCode());
    }

    public function testItAddsViolationIfChannelWithGivenCodeDoesNotExist(): void
    {
        $this->channelRepository
            ->method('findOneByCode')
            ->with('channel_code')
            ->willReturn(null)
        ;

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ channelCode }}', 'channel_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with((new ExistingChannelCode())->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate('channel_code', new ExistingChannelCode());
    }

    public function testItDoesNotAddViolationIfChannelWithGivenCodeExists(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->method('findOneByCode')
            ->with('channel_code')
            ->willReturn($channel)
        ;

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('channel_code', new ExistingChannelCode());
    }
}
