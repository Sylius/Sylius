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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefined;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefinedValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class HasAllPricesDefinedValidatorTest extends TestCase
{
    private ExecutionContextInterface&MockObject $executionContext;

    private HasAllPricesDefinedValidator $validator;

    protected function setUp(): void
    {
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new HasAllPricesDefinedValidator();
        $this->validator->initialize($this->executionContext);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->validator);
    }

    public function testItThrowsExceptionIfValueIsNotAProductVariant(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(new \stdClass(), new HasAllPricesDefined());
    }

    public function testItThrowsExceptionIfConstraintIsNotHasAllPricesDefined(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate(new \stdClass(), $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfProductVariantHasNoProduct(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $productVariant->method('getProduct')->willReturn(null);

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($productVariant, new HasAllPricesDefined());
    }

    public function testItAddsViolationIfChannelPricingIsMissing(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $channel1 = $this->createMock(ChannelInterface::class);
        $channel2 = $this->createMock(ChannelInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $constraint = new HasAllPricesDefined();

        $channel1->method('getCode')->willReturn('WEB');
        $channel2->method('getCode')->willReturn('MOBILE');

        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getChannelPricingForChannel')->willReturnMap([
            [$channel1, $channelPricing],
            [$channel2, null],
        ]);

        $channelPricing->method('getPrice')->willReturn(1000);

        $product->method('getChannels')->willReturn(new ArrayCollection([$channel1, $channel2]));

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->once())->method('addViolation');

        $this->executionContext
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate($productVariant, $constraint);
    }

    public function testItAddsViolationsIfPricesAreMissing(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $channel1 = $this->createMock(ChannelInterface::class);
        $channel2 = $this->createMock(ChannelInterface::class);
        $pricing1 = $this->createMock(ChannelPricingInterface::class);
        $pricing2 = $this->createMock(ChannelPricingInterface::class);
        $constraint = new HasAllPricesDefined();

        $channel1->method('getCode')->willReturn('WEB');
        $channel2->method('getCode')->willReturn('MOBILE');

        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getChannelPricingForChannel')->willReturnMap([
            [$channel1, $pricing1],
            [$channel2, $pricing2],
        ]);

        $pricing1->method('getPrice')->willReturn(null);
        $pricing2->method('getPrice')->willReturn(null);
        $product->method('getChannels')->willReturn(new ArrayCollection([$channel1, $channel2]));

        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violationBuilder->method('atPath')->willReturn($violationBuilder);
        $violationBuilder->expects($this->exactly(2))->method('addViolation');

        $this->executionContext
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $this->validator->validate($productVariant, $constraint);
    }

    public function testItDoesNotAddViolationIfAllPricesAreDefined(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);
        $product = $this->createMock(ProductInterface::class);
        $channel1 = $this->createMock(ChannelInterface::class);
        $channel2 = $this->createMock(ChannelInterface::class);
        $pricing1 = $this->createMock(ChannelPricingInterface::class);
        $pricing2 = $this->createMock(ChannelPricingInterface::class);
        $constraint = new HasAllPricesDefined();

        $channel1->method('getCode')->willReturn('WEB');
        $channel2->method('getCode')->willReturn('MOBILE');

        $productVariant->method('getProduct')->willReturn($product);
        $productVariant->method('getChannelPricingForChannel')->willReturnMap([
            [$channel1, $pricing1],
            [$channel2, $pricing2],
        ]);

        $pricing1->method('getPrice')->willReturn(1000);
        $pricing2->method('getPrice')->willReturn(2000);

        $product->method('getChannels')->willReturn(new ArrayCollection([$channel1, $channel2]));

        $this->executionContext->expects($this->never())->method('buildViolation');

        $this->validator->validate($productVariant, $constraint);
    }
}
