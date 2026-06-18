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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\PromotionConfigurationChannelCodes;
use Sylius\Bundle\CoreBundle\Validator\Constraints\PromotionConfigurationChannelCodesValidator;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

#[CoversClass(PromotionConfigurationChannelCodesValidator::class)]
final class PromotionConfigurationChannelCodesValidatorTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ExecutionContextInterface&MockObject $executionContext;

    private PromotionConfigurationChannelCodesValidator $validator;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new PromotionConfigurationChannelCodesValidator($this->channelRepository);
        $this->validator->initialize($this->executionContext);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(PromotionConfigurationChannelCodesValidator::class, $this->validator);
    }

    public function testItThrowsAnExceptionIfConstraintIsWrongType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate([], $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsNotAnArray(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate('not_an_array', new PromotionConfigurationChannelCodes());
    }

    public function testItDoesNothingIfChannelsKeyIsMissing(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(['count' => 5], new PromotionConfigurationChannelCodes());
    }

    public function testItDoesNothingIfChannelsListIsEmpty(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');
        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(
            [ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY => []],
            new PromotionConfigurationChannelCodes(),
        );
    }

    public function testItDoesNotAddViolationIfAllChannelCodesExist(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->expects($this->exactly(2))
            ->method('findOneByCode')
            ->willReturn($channel);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(
            [ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY => ['WEB', 'MOBILE']],
            new PromotionConfigurationChannelCodes(),
        );
    }

    public function testItAddsViolationForInvalidChannelCode(): void
    {
        $this->channelRepository
            ->expects($this->once())
            ->method('findOneByCode')
            ->with('INVALID_CODE')
            ->willReturn(null);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ channelCode }}', 'INVALID_CODE')
            ->willReturn($violationBuilder);
        $violationBuilder
            ->expects($this->once())
            ->method('atPath')
            ->with('[' . ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY . ']')
            ->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->with((new PromotionConfigurationChannelCodes())->invalidChannelCodeMessage)
            ->willReturn($violationBuilder);

        $this->validator->validate(
            [ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY => ['INVALID_CODE']],
            new PromotionConfigurationChannelCodes(),
        );
    }

    public function testItAddsOneViolationPerInvalidChannelCode(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->expects($this->exactly(3))
            ->method('findOneByCode')
            ->willReturnMap([
                ['WEB', $channel],
                ['INVALID_ONE', null],
                ['INVALID_TWO', null],
            ]);

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('setParameter')->willReturn($violationBuilder);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->exactly(2))->method('addViolation');

        $this->executionContext
            ->expects($this->exactly(2))
            ->method('buildViolation')
            ->willReturn($violationBuilder);

        $this->validator->validate(
            [ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY => ['WEB', 'INVALID_ONE', 'INVALID_TWO']],
            new PromotionConfigurationChannelCodes(),
        );
    }

    public function testItAddsViolationForNonStringChannelCode(): void
    {
        $this->channelRepository->expects($this->never())->method('findOneByCode');

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('setParameter')->willReturn($violationBuilder);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->expects($this->once())
            ->method('buildViolation')
            ->willReturn($violationBuilder);

        $this->validator->validate(
            [ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY => [42]],
            new PromotionConfigurationChannelCodes(),
        );
    }
}
