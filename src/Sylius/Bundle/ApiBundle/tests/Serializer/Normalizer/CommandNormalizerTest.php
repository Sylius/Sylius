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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Exception\InvalidRequestArgumentException;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\CommandNormalizer;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class CommandNormalizerTest extends TestCase
{
    private MockObject&NormalizerInterface $baseNormalizer;

    private CommandNormalizer $commandNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseNormalizer = $this->createMock(NormalizerInterface::class);
        $this->commandNormalizer = new CommandNormalizer($this->baseNormalizer);
    }

    public function testImplementsContextAwareNormalizerInterface(): void
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->commandNormalizer);
    }

    public function testSupportsNormalizationIfDataHasGetClassMethodAndItIsMissingConstructorArgumentsException(): void
    {
        self::assertTrue($this->commandNormalizer->supportsNormalization(
            new class() {
                public function getClass(): string
                {
                    return MissingConstructorArgumentsException::class;
                }
            },
        ));

        self::assertTrue($this->commandNormalizer->supportsNormalization(
            new class() {
                public function getClass(): string
                {
                    return InvalidRequestArgumentException::class;
                }
            },
        ));
    }

    public function testDoesNotSupportNormalizationIfDataHasNoGetClassMethod(): void
    {
        self::assertFalse($this->commandNormalizer->supportsNormalization(new \stdClass()));
    }

    public function testDoesNotSupportNormalizationIfDataClassIsNotMissingConstructorArgumentsException(): void
    {
        self::assertFalse($this->commandNormalizer->supportsNormalization(
            new class() {
                public function getClass(): string
                {
                    return \Exception::class;
                }
            },
        ));
    }

    public function testDoesNotSupportNormalizationIfNormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse($this->commandNormalizer->supportsNormalization(
            new class() {
                public function getClass(): string
                {
                    return MissingConstructorArgumentsException::class;
                }
            },
            null,
            ['sylius_command_normalizer_already_called' => true],
        ));
    }

    public function testDoesNotSupportNormalizationIfDataIsNotAnObject(): void
    {
        self::assertFalse($this->commandNormalizer->supportsNormalization('test'));
    }

    public function testNormalizesResponseForMissingConstructorArgumentsException(): void
    {
        /** @var stdClass|MockObject $objectMock */
        $objectMock = $this->createMock(\stdClass::class);

        $this->baseNormalizer->expects(self::once())
            ->method('normalize')
            ->with($objectMock, null, ['sylius_command_normalizer_already_called' => true])
            ->willReturn(['message' => 'Message']);

        self::assertSame(['code' => 400, 'message' => 'Message'], $this->commandNormalizer->normalize($objectMock));
    }
}
