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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Hydra\Serializer;

use ApiPlatform\Hydra\Serializer\CollectionFiltersNormalizer;
use ApiPlatform\JsonLd\Serializer\HydraPrefixTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @internal
 *
 * @see CollectionFiltersNormalizer
 *
 * Decorates the default behavior of the CollectionFiltersNormalizer
 * to remove empty search node when no particular data is present.
 */
final class EmptyCollectionFiltersNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use HydraPrefixTrait;

    private const SEARCH_SUFFIX = 'search';

    private const MAPPING_SUFFIX = 'mapping';

    private const TEMPLATE_SUFFIX = 'template';

    private const EMPTY_FILTERS_QUERY = '{?}';

    public function __construct(
        private readonly NormalizerInterface $decorated,
    ) {
    }

    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = [],
    ): array|\ArrayObject|bool|float|int|string|null {
        $data = $this->decorated->normalize($data, $format, $context);
        if (!is_array($data)) {
            return $data;
        }

        $hydraPrefix = $this->getHydraPrefix($context);

        $searchKey = $hydraPrefix . self::SEARCH_SUFFIX;
        $search = $data[$searchKey] ?? null;
        if (false === is_array($search) || [] === $search) {
            return $data;
        }

        $iri = $data['@id'] ?? '';
        if ('' === $iri) {
            return $data;
        }

        if (false === $this->hasFiltersDefined($hydraPrefix, $iri, $search)) {
            unset($data[$searchKey]);
        }

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return $this->decorated->getSupportedTypes($format);
    }

    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        if ($this->decorated instanceof NormalizerAwareInterface) {
            $this->decorated->setNormalizer($normalizer);
        }
    }

    private function hasFiltersDefined(string $hydraPrefix, string $iri, array $search): bool
    {
        $mapping = $search[$hydraPrefix . self::MAPPING_SUFFIX] ?? [];
        $searchTemplate = $search[$hydraPrefix . self::TEMPLATE_SUFFIX] ?? '';
        if ([] === $mapping && '' === $searchTemplate) {
            return false;
        }

        return false === $this->isSearchTemplateEmpty($iri, $searchTemplate);
    }

    private function isSearchTemplateEmpty(string $iri, string $searchTemplate): bool
    {
        return strtolower($iri . self::EMPTY_FILTERS_QUERY) === strtolower($searchTemplate);
    }
}
