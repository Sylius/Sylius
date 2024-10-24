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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ImageNormalizerSpec extends ObjectBehavior
{
    private const DEFAULT_FILTER = 'default';

    function it_is_a_context_aware_normalizer(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
        $this->shouldImplement(NormalizerAwareInterface::class);
    }

    function let(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($cacheManager, $requestStack, self::DEFAULT_FILTER);

        $this->setNormalizer($normalizer);
    }

    function it_supports_only_images(ImageInterface $image): void
    {
        $this->supportsNormalization(new \stdClass(), Argument::any())->shouldReturn(false);
        $this->supportsNormalization($image, Argument::any())->shouldReturn(true);
    }

    function it_does_not_support_if_the_normalizer_has_been_already_called(
        ImageInterface $image,
    ): void {
        $this->supportsNormalization(
            $image,
            Argument::any(),
            ['sylius_image_normalizer_already_called' => true],
        )->shouldReturn(false);
    }

    function it_resolves_image_path_based_on_default_filter_if_there_is_no_request(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['path' => 'some_path'])
        ;

        $requestStack->getCurrentRequest()->willReturn(null);

        $cacheManager
            ->getBrowserPath(parse_url('some_path', \PHP_URL_PATH), self::DEFAULT_FILTER)
            ->shouldBeCalled()
            ->willReturn('default_filter_path')
        ;

        $this->normalize($image)->shouldReturn(['path' => 'default_filter_path']);
    }

    function it_resolves_image_path_based_on_default_filter_if_there_no_image_filter_has_been_passed_via_request(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        Request $request,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['path' => 'some_path'])
        ;

        $request->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => '']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $cacheManager
            ->getBrowserPath(parse_url('some_path', \PHP_URL_PATH), self::DEFAULT_FILTER)
            ->shouldBeCalled()
            ->willReturn('default_filter_path')
        ;

        $this->normalize($image)->shouldReturn(['path' => 'default_filter_path']);
    }

    function it_throws_validation_exception_when_passing_an_invalid_image_filter(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        Request $request,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['path' => 'some_path'])
        ;

        $request->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $cacheManager
            ->getBrowserPath(parse_url('some_path', \PHP_URL_PATH), 'invalid')
            ->shouldBeCalled()
            ->willThrow(NonExistingFilterException::class)
        ;

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('normalize', [$image, null, []])
        ;
    }

    function it_throws_validation_exception_when_resolver_for_passed_filter_could_not_been_found(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        Request $request,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['path' => 'some_path'])
        ;

        $request->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'no-resolver-filter']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $cacheManager
            ->getBrowserPath(parse_url('some_path', \PHP_URL_PATH), 'no-resolver-filter')
            ->shouldBeCalled()
            ->willThrow(\OutOfBoundsException::class)
        ;

        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->during('normalize', [$image, null, []])
        ;
    }

    function it_does_not_resolve_image_path_if_path_is_not_serialized(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['id' => 1])
        ;

        $this->normalize($image, null, [])->shouldReturn(['id' => 1]);

        $cacheManager->getBrowserPath(Argument::any(), Argument::any())->shouldNotHaveBeenCalled();
        $requestStack->getCurrentRequest()->shouldNotHaveBeenCalled();
    }

    function it_applies_given_image_filter(
        CacheManager $cacheManager,
        RequestStack $requestStack,
        NormalizerInterface $normalizer,
        Request $request,
        ImageInterface $image,
    ): void {
        $normalizer
            ->normalize($image, null, ['sylius_image_normalizer_already_called' => true])
            ->shouldBeCalled()
            ->willReturn(['path' => 'some_path'])
        ;

        $request->query = new InputBag([ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_large']);
        $requestStack->getCurrentRequest()->willReturn($request);

        $cacheManager
            ->getBrowserPath(parse_url('some_path', \PHP_URL_PATH), 'sylius_large')
            ->shouldBeCalled()
            ->willReturn('large_filter_path')
        ;

        $this->normalize($image)->shouldReturn(['path' => 'large_filter_path']);
    }
}
