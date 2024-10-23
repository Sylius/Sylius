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

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Exception\InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Filter\NonExistingFilterException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

class ImageNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public const FILTER_QUERY_PARAMETER = 'imageFilter';

    private const ALREADY_CALLED = 'sylius_image_normalizer_already_called';

    public function __construct(
        private readonly CacheManager $cacheManager,
        private readonly RequestStack $requestStack,
        private readonly string $defaultFilter,
    ) {
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ImageInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        /** @var array<string, string> $data */
        $data = $this->normalizer->normalize($object, $format, $context);

        if (true === array_key_exists('path', $data)) {
            $data['path'] = $this->resolvePath($data);
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ImageInterface;
    }

    /** @param array<string, string> $data */
    protected function resolvePath(array $data): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $filter = $request?->query->get(self::FILTER_QUERY_PARAMETER, '');

        if (null === $filter || '' === $filter) {
            $filter = $this->defaultFilter;
        }

        try {
            return $this->cacheManager->getBrowserPath(parse_url($data['path'], \PHP_URL_PATH), $filter);
        } catch (NonExistingFilterException|\OutOfBoundsException) {
            throw new InvalidArgumentException(sprintf('Filter "%s" is not configured.', $filter));
        }
    }
}
