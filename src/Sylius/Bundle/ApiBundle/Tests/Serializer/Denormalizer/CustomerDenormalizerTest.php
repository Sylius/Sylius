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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CustomerDenormalizer;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CustomerDenormalizerTest extends TestCase
{
    private ClockInterface&MockObject $clock;

    private CustomerDenormalizer $customerDenormalizer;

    private const ALREADY_CALLED = 'sylius_customer_denormalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clock = $this->createMock(ClockInterface::class);
        $this->customerDenormalizer = new CustomerDenormalizer($this->clock);
    }

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse(
            $this->customerDenormalizer->supportsDenormalization(
                [],
                CustomerInterface::class,
                context: [self::ALREADY_CALLED => true],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse(
            $this->customerDenormalizer->supportsDenormalization('string', CustomerInterface::class),
        );
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotACustomer(): void
    {
        self::assertFalse($this->customerDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testDoesNothingIfUserVerifiedIsNotSet(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);

        $this->customerDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with([], CustomerInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($customerMock);

        self::assertSame($customerMock, $this->customerDenormalizer->denormalize([], CustomerInterface::class));

        $this->clock->expects(self::never())->method('now');
    }

    public function testChangesUserVerifiedFromFalseToNull(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->customerDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(['user' => ['verified' => null]], CustomerInterface::class, null, [self::ALREADY_CALLED => true])
            ->willReturn($customerMock);

        self::assertSame(
            $customerMock,
            $this->customerDenormalizer->denormalize(
                ['user' => ['verified' => false]],
                CustomerInterface::class,
            ),
        );

        $this->clock->expects(self::never())->method('now');
    }

    public function testChangesUserVerifiedFromTrueToDatetime(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);

        $this->customerDenormalizer->setDenormalizer($denormalizerMock);

        $dateTime = new \DateTimeImmutable('2021-01-01T00:00:00+00:00');

        $this->clock->expects(self::once())->method('now')->willReturn($dateTime);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with(
                ['user' => ['verified' => '2021-01-01T00:00:00+00:00']],
                CustomerInterface::class,
                null,
                [self::ALREADY_CALLED => true],
            )->willReturn($customerMock);

        self::assertSame(
            $customerMock,
            $this->customerDenormalizer->denormalize(['user' => ['verified' => true]], CustomerInterface::class),
        );
    }
}
