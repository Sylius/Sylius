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

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ImageNormalizerTest extends TestCase
{
    private CacheManager&MockObject $cacheManager;

    private MockObject&RequestStack $requestStack;

    private MockObject&NormalizerInterface $normalizer;

    private ImageNormalizer $imageNormalizer;

    private const DEFAULT_FILTER = 'default';

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheManager = $this->createMock(CacheManager::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);

        $this->imageNormalizer = new ImageNormalizer(
            $this->cacheManager,
            $this->requestStack,
            self::DEFAULT_FILTER,
        );
        $this->imageNormalizer->setNormalizer($this->normalizer);
    }

    public function testItImplementsNormalizerInterfaces(): void
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->imageNormalizer);

        self::assertInstanceOf(NormalizerAwareInterface::class, $this->imageNormalizer);
    }

    public function testSupportsOnlyImages(): void
    {
        $imageMock = $this->createMock(ImageInterface::class);

        self::assertFalse($this->imageNormalizer->supportsNormalization(new \stdClass()));

        self::assertTrue($this->imageNormalizer->supportsNormalization($imageMock));
    }

    public function testDoesNotSupportIfAlreadyCalled(): void
    {
        $imageMock = $this->createMock(ImageInterface::class);

        self::assertFalse($this->imageNormalizer->supportsNormalization(
            $imageMock,
            null,
            ['sylius_image_normalizer_already_called' => true],
        ));
    }

    public function testResolvesPathBasedOnDefaultFilterWhenNoRequestAvailable(): void
    {
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['path' => 'some_path']);

        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->cacheManager->expects(self::once())
            ->method('getBrowserPath')
            ->with(parse_url('some_path', \PHP_URL_PATH), self::DEFAULT_FILTER)
            ->willReturn('default_filter_path');

        self::assertSame(['path' => 'default_filter_path'], $this->imageNormalizer->normalize($imageMock));
    }

    public function testResolvesPathBasedOnDefaultFilterWhenNoFilterInRequest(): void
    {
        $requestMock = $this->createMock(Request::class);
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['path' => 'some_path']);

        $requestMock->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => '']);
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);

        $this->cacheManager->expects(self::once())
            ->method('getBrowserPath')
            ->with(parse_url('some_path', \PHP_URL_PATH), self::DEFAULT_FILTER)
            ->willReturn('default_filter_path');

        self::assertSame(['path' => 'default_filter_path'], $this->imageNormalizer->normalize($imageMock));
    }

    public function testThrowsValidationExceptionForInvalidFilter(): void
    {
        $requestMock = $this->createMock(Request::class);
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['path' => 'some_path']);

        $requestMock->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid']);
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);

        $this->cacheManager->expects(self::once())
            ->method('getBrowserPath')
            ->with(parse_url('some_path', \PHP_URL_PATH), 'invalid')
            ->willThrowException(new NonExistingFilterException());

        self::expectException(InvalidArgumentException::class);
        $this->imageNormalizer->normalize($imageMock);
    }

    public function testThrowsValidationExceptionWhenFilterNotResolvable(): void
    {
        $requestMock = $this->createMock(Request::class);
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['path' => 'some_path']);

        $requestMock->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid_filter']);
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);

        $this->cacheManager->expects(self::once())
            ->method('getBrowserPath')
            ->with(parse_url('some_path', \PHP_URL_PATH), 'invalid_filter')
            ->willThrowException(new \OutOfBoundsException());

        self::expectException(InvalidArgumentException::class);
        $this->imageNormalizer->normalize($imageMock);
    }

    public function testDoesNotResolvePathIfPathNotSerialized(): void
    {
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['id' => 1]);

        self::assertSame(['id' => 1], $this->imageNormalizer->normalize($imageMock));

        $this->cacheManager->expects(self::never())->method('getBrowserPath');
        $this->requestStack->expects(self::never())->method('getCurrentRequest');
    }

    public function testAppliesCustomFilter(): void
    {
        $requestMock = $this->createMock(Request::class);
        $imageMock = $this->createMock(ImageInterface::class);

        $this->normalizer->expects(self::once())
            ->method('normalize')
            ->with($imageMock, null, ['sylius_image_normalizer_already_called' => true])
            ->willReturn(['path' => 'some_path']);

        $requestMock->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'custom_filter']);
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);

        $this->cacheManager->expects(self::once())
            ->method('getBrowserPath')
            ->with(parse_url('some_path', \PHP_URL_PATH), 'custom_filter')
            ->willReturn('custom_filter_path');

        self::assertSame(['path' => 'custom_filter_path'], $this->imageNormalizer->normalize($imageMock));
    }
}
