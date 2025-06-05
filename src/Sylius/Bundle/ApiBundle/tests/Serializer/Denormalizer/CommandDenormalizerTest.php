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
use Sylius\Bundle\ApiBundle\Command\Account\RegisterShopUser;
use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandDenormalizerTest extends TestCase
{
    private DenormalizerInterface&MockObject $baseNormalizer;

    private AdvancedNameConverterInterface&MockObject $nameConverter;

    private CommandDenormalizer $commandDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseNormalizer = $this->createMock(DenormalizerInterface::class);
        $this->nameConverter = $this->createMock(AdvancedNameConverterInterface::class);
        $this->commandDenormalizer = new CommandDenormalizer($this->baseNormalizer, $this->nameConverter);
    }

    public function testImplementsContextAwareDenormalizerInterface(): void
    {
        self::assertInstanceOf(DenormalizerInterface::class, $this->commandDenormalizer);
    }

    public function testSupportsDenormalizationForSpecifiedInputClass(): void
    {
        self::assertTrue(
            $this->commandDenormalizer->supportsDenormalization(
                null,
                '',
                context: ['input' => ['class' => 'Class']],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationForNotSpecifiedInputClass(): void
    {
        self::assertFalse($this->commandDenormalizer->supportsDenormalization(null, ''));
    }

    public function testThrowsExceptionIfNotAllRequiredParametersArePresentInTheContext(): void
    {
        $exception = new MissingConstructorArgumentsException(
            '',
            400,
            null,
            ['firstName', 'lastName'],
        );

        $context = ['input' => ['class' => RegisterShopUser::class]];

        $data = ['email' => 'test@example.com', 'password' => 'pa$$word'];

        $this->nameConverter
            ->expects(self::exactly(2))
            ->method('normalize')
            ->willReturnMap([
                ['firstName', RegisterShopUser::class, null, [], 'first_name'],
                ['lastName', RegisterShopUser::class, null, [], 'lastName'],
            ]);

        $this->baseNormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, '', null, $context)
            ->willThrowException($exception);

        self::expectException(MissingConstructorArgumentsException::class);
        self::expectExceptionMessage('Request does not have the following required fields specified: first_name, lastName.');

        $this->commandDenormalizer->denormalize($data, '', null, $context);
    }

    public function testThrowsExceptionForMismatchedArgumentType(): void
    {
        $previousException = NotNormalizableValueException::createForUnexpectedDataType(
            '',
            1,
            ['string'],
            'firstName',
        );

        $exception = new UnexpectedValueException('', 400, $previousException);

        $context = ['input' => ['class' => RegisterShopUser::class]];

        $data = ['firstName' => 1];

        $this->nameConverter
            ->expects(self::once())
            ->method('normalize')
            ->with('firstName', class: RegisterShopUser::class)
            ->willReturn('first_name');

        $this->baseNormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, '', null, $context)
            ->willThrowException($exception);

        self::expectException(InvalidRequestArgumentException::class);

        self::expectExceptionMessage('Request field "first_name" should be of type "string".');

        $this->commandDenormalizer->denormalize($data, '', null, $context);
    }

    public function testThrowsTheSameExceptionIfPreviousExceptionIsNotNormalizableValueException(): void
    {
        $exception = new UnexpectedValueException('Unexpected value');

        $context = ['input' => ['class' => RegisterShopUser::class]];

        $data = ['firstName' => '1'];

        $this->baseNormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, '', null, $context)
            ->willThrowException($exception);

        self::expectException(UnexpectedValueException::class);

        self::expectExceptionMessage('Unexpected value');

        $this->commandDenormalizer->denormalize($data, '', null, $context);
    }
}
